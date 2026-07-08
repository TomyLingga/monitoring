<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomingCpo;
use App\Models\Storage;
use App\Models\MasterProduk;
use App\Models\StokProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingCpoController extends Controller
{
    public function index()
    {
        return IncomingCpo::with(['kontrakCpo.supplier', 'storage'])->latest('tgl')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'storage_id' => 'required|exists:storages,id',
            'qty_kirim' => 'required|numeric|min:0',
            'qty_terima' => 'required|numeric|min:0',
            'tgl' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $data['selisih_qty'] = $data['qty_kirim'] - $data['qty_terima'];

        // Storage specific check
        $storage = Storage::findOrFail($data['storage_id']);
        $cpoProduct = MasterProduk::where('kode_produk', 'CPO')->first();
        if (!$cpoProduct) {
            return response()->json(['message' => 'Produk CPO tidak ditemukan di database.'], 422);
        }

        if ($storage->jenis === 'tangki') {
            // Tangki spesifik 1 produk. Cek jika sudah terisi produk lain
            $hasOtherProduct = StokProduk::where('storage_id', $storage->id)
                ->where('produk_id', '!=', $cpoProduct->id)
                ->where('qty', '>', 0)
                ->exists();
            if ($hasOtherProduct) {
                return response()->json(['message' => 'Gagal: Tangki ini spesifik untuk produk lain dan sudah terisi.'], 422);
            }

            // Kapasitas Check
            $currentStock = StokProduk::where('storage_id', $storage->id)->sum('qty');
            $remainingCapacity = $storage->kapasitas - $currentStock;
            if ($data['qty_terima'] > $remainingCapacity) {
                return response()->json([
                    'message' => 'Gagal: Qty Terima melebihi sisa kapasitas tangki.',
                    'remaining_capacity' => $remainingCapacity
                ], 422);
            }
        }

        return DB::transaction(function () use ($data, $cpoProduct) {
            $incoming = IncomingCpo::create($data);

            // Update CPO Stock in Storage
            $stok = StokProduk::firstOrNew([
                'storage_id' => $data['storage_id'],
                'produk_id' => $cpoProduct->id
            ]);
            $stok->qty = ($stok->qty ?? 0) + $data['qty_terima'];
            $stok->save();

            return $incoming->load(['kontrakCpo.supplier', 'storage']);
        });
    }

    public function show(IncomingCpo $incomingCpo)
    {
        return $incomingCpo->load(['kontrakCpo.supplier', 'storage']);
    }

    public function update(Request $request, IncomingCpo $incomingCpo)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'storage_id' => 'required|exists:storages,id',
            'qty_kirim' => 'required|numeric|min:0',
            'qty_terima' => 'required|numeric|min:0',
            'tgl' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $data['selisih_qty'] = $data['qty_kirim'] - $data['qty_terima'];

        // Storage specific check
        $storage = Storage::findOrFail($data['storage_id']);
        $cpoProduct = MasterProduk::where('kode_produk', 'CPO')->first();

        if ($storage->jenis === 'tangki') {
            $hasOtherProduct = StokProduk::where('storage_id', $storage->id)
                ->where('produk_id', '!=', $cpoProduct->id)
                ->where('qty', '>', 0)
                ->exists();
            if ($hasOtherProduct) {
                return response()->json(['message' => 'Gagal: Tangki ini spesifik untuk produk lain dan sudah terisi.'], 422);
            }

            // Kapasitas Check
            $currentStock = StokProduk::where('storage_id', $storage->id)->sum('qty');
            $remainingCapacity = $storage->kapasitas - ($currentStock - $incomingCpo->qty_terima);
            if ($data['qty_terima'] > $remainingCapacity) {
                return response()->json([
                    'message' => 'Gagal: Qty Terima melebihi sisa kapasitas tangki.',
                    'remaining_capacity' => $remainingCapacity
                ], 422);
            }
        }

        return DB::transaction(function () use ($data, $incomingCpo, $cpoProduct) {
            // Revert old stock
            $oldStok = StokProduk::where('storage_id', $incomingCpo->storage_id)
                ->where('produk_id', $cpoProduct->id)
                ->first();
            if ($oldStok) {
                $oldStok->qty = max(0, $oldStok->qty - $incomingCpo->qty_terima);
                $oldStok->save();
            }

            // Apply new details
            $incomingCpo->update($data);

            // Apply new stock
            $newStok = StokProduk::firstOrNew([
                'storage_id' => $data['storage_id'],
                'produk_id' => $cpoProduct->id
            ]);
            $newStok->qty = ($newStok->qty ?? 0) + $data['qty_terima'];
            $newStok->save();

            return $incomingCpo->load(['kontrakCpo.supplier', 'storage']);
        });
    }

    public function destroy(IncomingCpo $incomingCpo)
    {
        return DB::transaction(function () use ($incomingCpo) {
            $cpoProduct = MasterProduk::where('kode_produk', 'CPO')->first();
            if ($cpoProduct) {
                $stok = StokProduk::where('storage_id', $incomingCpo->storage_id)
                    ->where('produk_id', $cpoProduct->id)
                    ->first();
                if ($stok) {
                    $stok->qty = max(0, $stok->qty - $incomingCpo->qty_terima);
                    $stok->save();
                }
            }

            $incomingCpo->delete();
            return response()->json(['message' => 'Incoming CPO berhasil dihapus']);
        });
    }
}
