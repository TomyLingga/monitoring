<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengirimanPenjualan;
use App\Models\KontrakPenjualan;
use App\Models\StokProduk;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengirimanPenjualan::with(['kontrakPenjualan.buyer', 'kontrakPenjualan.produk', 'storage', 'invoices'])
            ->orderBy('tgl', 'desc');

        if ($request->start_date) {
            $query->where('tgl', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('tgl', '<=', $request->end_date);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'qty_kirim'            => 'required|numeric|min:0',
            'qty_terima'           => 'nullable|numeric|min:0',
            'via'                  => 'nullable|string',
            'termin'               => 'nullable|string',
            'status'               => 'nullable|string',
            'incoterm'             => 'nullable|string',
            'tgl'                  => 'required|date',
            'storage_id'           => 'nullable|exists:storages,id',
            'create_invoice'       => 'nullable|boolean',
            'nomor_invoice'        => 'nullable|string',
            'nilai_invoice'        => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $kontrak = KontrakPenjualan::findOrFail($request->kontrak_penjualan_id);
            $qtyKirim = (float) $request->qty_kirim;
            $productId = $kontrak->produk_id;

            // 1. Allocate and deduct stock
            $deductions = $this->allocateAndDeductStock($productId, $qtyKirim, $request->storage_id);

            // 2. Create Shipment record
            $shipment = PengirimanPenjualan::create([
                'kontrak_penjualan_id' => $kontrak->id,
                'qty_kirim'            => $qtyKirim,
                'qty_terima'           => $request->qty_terima ?? $qtyKirim,
                'via'                  => $request->via,
                'termin'               => $request->termin,
                'status'               => $request->status ?? 'Dikirim',
                'incoterm'             => $request->incoterm,
                'tgl'                  => $request->tgl,
                'storage_id'           => $request->storage_id,
            ]);

            foreach ($deductions as $d) {
                $shipment->storageSources()->create($d);
            }

            // 3. Create Invoice if requested
            if ($request->create_invoice && $request->nomor_invoice) {
                Invoice::create([
                    'pengiriman_id' => $shipment->id,
                    'nomor_invoice' => $request->nomor_invoice,
                    'nilai'         => $request->nilai_invoice ?? ($qtyKirim * (float)$kontrak->harga_satuan),
                    'tgl_invoice'   => $request->tgl,
                    'status'        => 'draft',
                ]);
            }

            return response()->json($shipment->load(['kontrakPenjualan.buyer', 'kontrakPenjualan.produk', 'storage', 'invoices']), 201);
        });
    }

    public function show($id)
    {
        return response()->json(
            PengirimanPenjualan::with(['kontrakPenjualan.buyer', 'kontrakPenjualan.produk', 'storage', 'invoices', 'storageSources.storage'])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $shipment = PengirimanPenjualan::findOrFail($id);

        $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'qty_kirim'            => 'required|numeric|min:0',
            'qty_terima'           => 'nullable|numeric|min:0',
            'via'                  => 'nullable|string',
            'termin'               => 'nullable|string',
            'status'               => 'nullable|string',
            'incoterm'             => 'nullable|string',
            'tgl'                  => 'required|date',
            'storage_id'           => 'nullable|exists:storages,id',
            'nilai_invoice'        => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $shipment) {
            $kontrak = KontrakPenjualan::findOrFail($shipment->kontrak_penjualan_id);
            $productId = $kontrak->produk_id;

            // Revert old shipment stock
            $this->revertStock($shipment);

            // Allocate new stock
            $deductions = $this->allocateAndDeductStock($productId, (float)$request->qty_kirim, $request->storage_id);

            $shipment->update([
                'kontrak_penjualan_id' => $request->kontrak_penjualan_id,
                'qty_kirim'            => $request->qty_kirim,
                'qty_terima'           => $request->qty_terima ?? $request->qty_kirim,
                'via'                  => $request->via,
                'termin'               => $request->termin,
                'status'               => $request->status ?? $shipment->status,
                'incoterm'             => $request->incoterm,
                'tgl'                  => $request->tgl,
                'storage_id'           => $request->storage_id,
            ]);

            foreach ($deductions as $d) {
                $shipment->storageSources()->create($d);
            }

            // Update associated invoice value if it exists
            $invoice = $shipment->invoices()->first();
            if ($invoice) {
                $invoice->update([
                    'nilai' => $request->nilai_invoice ?? ((float)$request->qty_kirim * (float)$kontrak->harga_satuan),
                ]);
            }

            return response()->json($shipment->load(['kontrakPenjualan.buyer', 'kontrakPenjualan.produk', 'storage', 'invoices']));
        });
    }

    public function destroy($id)
    {
        $shipment = PengirimanPenjualan::findOrFail($id);

        DB::transaction(function () use ($shipment) {
            $this->revertStock($shipment);
            $shipment->delete();
        });

        return response()->json(['message' => 'Pengiriman penjualan dihapus']);
    }

    private function allocateAndDeductStock($productId, $qty, $preferredStorageId = null)
    {
        $remaining = $qty;
        $deductions = [];

        if ($preferredStorageId) {
            $stok = StokProduk::where('produk_id', $productId)
                ->where('storage_id', $preferredStorageId)
                ->first();

            if ($stok && $stok->qty > 0) {
                $toDeduct = min($remaining, (float)$stok->qty);
                $stok->decrement('qty', $toDeduct);
                $remaining -= $toDeduct;
                $deductions[] = ['storage_id' => $preferredStorageId, 'qty' => $toDeduct];
            }
        }

        if ($remaining > 0) {
            $stocks = StokProduk::where('produk_id', $productId)
                ->where('qty', '>', 0)
                ->when($preferredStorageId, function ($query) use ($preferredStorageId) {
                    return $query->where('storage_id', '!=', $preferredStorageId);
                })
                ->orderBy('qty', 'asc') // Paling sedikit dulu
                ->get();

            foreach ($stocks as $stok) {
                if ($remaining <= 0) break;

                $toDeduct = min($remaining, (float)$stok->qty);
                $stok->decrement('qty', $toDeduct);
                $remaining -= $toDeduct;
                $deductions[] = ['storage_id' => $stok->storage_id, 'qty' => $toDeduct];
            }
        }

        if ($remaining > 0) {
            $fallbackStorageId = $preferredStorageId;
            if (!$fallbackStorageId) {
                $product = \App\Models\MasterProduk::find($productId);
                $isLiquid = $product && in_array($product->kode_produk, ['CPO', 'RBDPO', 'PFAD', 'Stearin', 'OL-IV56', 'OL-IV57', 'OL-IV58', 'OL-IV60']);
                $fallbackStorage = \App\Models\Storage::where('jenis', $isLiquid ? 'tangki' : 'gudang')->first();
                if ($fallbackStorage) {
                    $fallbackStorageId = $fallbackStorage->id;
                }
            }

            if ($fallbackStorageId) {
                $stok = StokProduk::firstOrCreate(
                    ['produk_id' => $productId, 'storage_id' => $fallbackStorageId],
                    ['qty' => 0]
                );
                $stok->decrement('qty', $remaining);
                $deductions[] = ['storage_id' => $fallbackStorageId, 'qty' => $remaining];
            }
        }

        return $deductions;
    }

    private function revertStock($shipment)
    {
        $productId = $shipment->kontrakPenjualan->produk_id;

        if ($shipment->storageSources()->exists()) {
            foreach ($shipment->storageSources as $source) {
                $stok = StokProduk::firstOrCreate(
                    ['produk_id' => $productId, 'storage_id' => $source->storage_id],
                    ['qty' => 0]
                );
                $stok->increment('qty', $source->qty);
            }
            $shipment->storageSources()->delete();
        } else {
            // Legacy revert
            if ($shipment->storage_id) {
                $stok = StokProduk::where('produk_id', $productId)
                    ->where('storage_id', $shipment->storage_id)
                    ->first();
                if ($stok) {
                    $stok->increment('qty', $shipment->qty_kirim);
                    return;
                }
            }

            $stok = StokProduk::where('produk_id', $productId)->first();
            if ($stok) {
                $stok->increment('qty', $shipment->qty_kirim);
            }
        }
    }
}
