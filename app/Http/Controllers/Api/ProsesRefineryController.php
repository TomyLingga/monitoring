<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProsesRefinery;
use App\Models\BahanRefinery;
use App\Models\HasilRefinery;
use App\Models\StokProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesRefineryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProsesRefinery::with([
            'bahanRefineries.produk',
            'bahanRefineries.storage',
            'hasilRefineries.produk',
            'hasilRefineries.storage',
        ])->orderBy('tgl', 'desc');

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
            'tgl'                 => 'required|date',
            'bahan'               => 'array',
            'bahan.*.produk_id'   => 'required|exists:master_produks,id',
            'bahan.*.qty'         => 'required|numeric|min:0',
            'bahan.*.storage_id'  => 'nullable|exists:storages,id',
            'hasil'               => 'array',
            'hasil.*.produk_id'   => 'required|exists:master_produks,id',
            'hasil.*.qty'         => 'required|numeric|min:0',
            'hasil.*.storage_id'  => 'nullable|exists:storages,id',
        ]);

        return DB::transaction(function () use ($request) {
            $proses = ProsesRefinery::create([
                'tgl'     => $request->tgl,
                'shift'   => $request->shift,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->bahan ?? [] as $b) {
                $this->allocateBahan(
                    $proses->id,
                    $b['produk_id'],
                    $b['qty'],
                    BahanRefinery::class,
                    'proses_refinery_id',
                    $b['storage_id'] ?? null
                );
            }

            foreach ($request->hasil ?? [] as $h) {
                $this->allocateHasil(
                    $proses->id,
                    $h['produk_id'],
                    $h['qty'],
                    HasilRefinery::class,
                    'proses_refinery_id',
                    $h['storage_id'] ?? null
                );
            }

            return response()->json($proses->load([
                'bahanRefineries.produk',
                'bahanRefineries.storage',
                'hasilRefineries.produk',
                'hasilRefineries.storage'
            ]), 201);
        });
    }

    public function show($id)
    {
        $proses = ProsesRefinery::with([
            'bahanRefineries.produk',
            'bahanRefineries.storage',
            'hasilRefineries.produk',
            'hasilRefineries.storage'
        ])->findOrFail($id);
        return response()->json($proses);
    }

    public function update(Request $request, $id)
    {
        $proses = ProsesRefinery::findOrFail($id);

        $request->validate([
            'tgl'                 => 'required|date',
            'bahan'               => 'array',
            'bahan.*.produk_id'   => 'required|exists:master_produks,id',
            'bahan.*.qty'         => 'required|numeric|min:0',
            'bahan.*.storage_id'  => 'nullable|exists:storages,id',
            'hasil'               => 'array',
            'hasil.*.produk_id'   => 'required|exists:master_produks,id',
            'hasil.*.qty'         => 'required|numeric|min:0',
            'hasil.*.storage_id'  => 'nullable|exists:storages,id',
        ]);

        return DB::transaction(function () use ($request, $proses) {
            $proses->update([
                'tgl'     => $request->tgl ?? $proses->tgl,
                'shift'   => $request->shift ?? $proses->shift,
                'catatan' => $request->catatan ?? $proses->catatan,
            ]);

            // Revert old bahans and delete
            if ($request->has('bahan')) {
                $oldBahans = BahanRefinery::where('proses_refinery_id', $proses->id)->get();
                foreach ($oldBahans as $ob) {
                    $stok = StokProduk::where('produk_id', $ob->produk_id)
                        ->where('storage_id', $ob->storage_id)
                        ->first();
                    if ($stok) {
                        $stok->increment('qty', $ob->qty);
                    }
                }
                BahanRefinery::where('proses_refinery_id', $proses->id)->delete();

                // Allocate new bahans
                foreach ($request->bahan as $b) {
                    $this->allocateBahan(
                        $proses->id,
                        $b['produk_id'],
                        $b['qty'],
                        BahanRefinery::class,
                        'proses_refinery_id',
                        $b['storage_id'] ?? null
                    );
                }
            }

            // Revert old hasils and delete
            if ($request->has('hasil')) {
                $oldHasils = HasilRefinery::where('proses_refinery_id', $proses->id)->get();
                foreach ($oldHasils as $oh) {
                    $stok = StokProduk::where('produk_id', $oh->produk_id)
                        ->where('storage_id', $oh->storage_id)
                        ->first();
                    if ($stok) {
                        $stok->decrement('qty', $oh->qty);
                    }
                }
                HasilRefinery::where('proses_refinery_id', $proses->id)->delete();

                // Allocate new hasils
                foreach ($request->hasil as $h) {
                    $this->allocateHasil(
                        $proses->id,
                        $h['produk_id'],
                        $h['qty'],
                        HasilRefinery::class,
                        'proses_refinery_id',
                        $h['storage_id'] ?? null
                    );
                }
            }

            return response()->json($proses->load([
                'bahanRefineries.produk',
                'bahanRefineries.storage',
                'hasilRefineries.produk',
                'hasilRefineries.storage'
            ]));
        });
    }

    public function destroy($id)
    {
        $proses = ProsesRefinery::findOrFail($id);

        DB::transaction(function () use ($proses) {
            // Revert bahans stock
            foreach ($proses->bahanRefineries as $ob) {
                $stok = StokProduk::where('produk_id', $ob->produk_id)
                    ->where('storage_id', $ob->storage_id)
                    ->first();
                if ($stok) {
                    $stok->increment('qty', $ob->qty);
                }
            }

            // Revert hasils stock
            foreach ($proses->hasilRefineries as $oh) {
                $stok = StokProduk::where('produk_id', $oh->produk_id)
                    ->where('storage_id', $oh->storage_id)
                    ->first();
                if ($stok) {
                    $stok->decrement('qty', $oh->qty);
                }
            }

            $proses->delete();
        });

        return response()->json(['message' => 'Proses refinery dihapus']);
    }

    private function allocateBahan($prosesId, $produkId, $requiredQty, $modelClass, $foreignKey, $preferredStorageId = null)
    {
        $product = \App\Models\MasterProduk::findOrFail($produkId);
        $remaining = $requiredQty;

        if ($preferredStorageId) {
            $stok = StokProduk::where('produk_id', $produkId)
                ->where('storage_id', $preferredStorageId)
                ->first();
            
            if ($stok && $stok->qty > 0) {
                $toDeduct = min($remaining, $stok->qty);
                $stok->decrement('qty', $toDeduct);
                
                $modelClass::create([
                    $foreignKey  => $prosesId,
                    'produk_id'  => $produkId,
                    'qty'        => $toDeduct,
                    'storage_id' => $preferredStorageId,
                ]);
                
                $remaining -= $toDeduct;
            }
        }

        if ($remaining > 0) {
            $stocks = StokProduk::where('produk_id', $produkId)
                ->where('qty', '>', 0)
                ->when($preferredStorageId, function ($query) use ($preferredStorageId) {
                    return $query->where('storage_id', '!=', $preferredStorageId);
                })
                ->orderBy('qty', 'asc')
                ->get();
                
            foreach ($stocks as $stok) {
                if ($remaining <= 0) break;
                
                $toDeduct = min($remaining, $stok->qty);
                $stok->decrement('qty', $toDeduct);
                
                $modelClass::create([
                    $foreignKey  => $prosesId,
                    'produk_id'  => $produkId,
                    'qty'        => $toDeduct,
                    'storage_id' => $stok->storage_id,
                ]);
                
                $remaining -= $toDeduct;
            }
        }

        if ($remaining > 0) {
            $fallbackStorageId = $preferredStorageId;
            if (!$fallbackStorageId) {
                $isLiquid = in_array($product->kode_produk, ['CPO', 'RBDPO', 'PFAD', 'Stearin', 'OL-IV56', 'OL-IV57', 'OL-IV58', 'OL-IV60']);
                $fallbackStorage = \App\Models\Storage::where('jenis', $isLiquid ? 'tangki' : 'gudang')->first();
                if ($fallbackStorage) {
                    $fallbackStorageId = $fallbackStorage->id;
                }
            }
            
            if ($fallbackStorageId) {
                $stok = StokProduk::firstOrCreate(
                    ['produk_id' => $produkId, 'storage_id' => $fallbackStorageId],
                    ['qty' => 0]
                );
                $stok->decrement('qty', $remaining);
                
                $modelClass::create([
                    $foreignKey  => $prosesId,
                    'produk_id'  => $produkId,
                    'qty'        => $remaining,
                    'storage_id' => $fallbackStorageId,
                ]);
            }
        }
    }

    private function allocateHasil($prosesId, $produkId, $qty, $modelClass, $foreignKey, $preferredStorageId = null)
    {
        $product = \App\Models\MasterProduk::findOrFail($produkId);
        $isLiquid = in_array($product->kode_produk, ['CPO', 'RBDPO', 'PFAD', 'Stearin', 'OL-IV56', 'OL-IV57', 'OL-IV58', 'OL-IV60']);
        $remaining = $qty;

        if ($preferredStorageId) {
            $storage = \App\Models\Storage::find($preferredStorageId);
            if ($storage) {
                $currentStock = StokProduk::where('storage_id', $storage->id)->sum('qty');
                $capacity = (float) $storage->kapasitas;
                $space = max(0.0, $capacity - $currentStock);
                
                if ($space > 0) {
                    $toAdd = min($remaining, $space);
                    $stok = StokProduk::firstOrCreate(
                        ['produk_id' => $produkId, 'storage_id' => $storage->id],
                        ['qty' => 0]
                    );
                    $stok->increment('qty', $toAdd);
                    
                    $modelClass::create([
                        $foreignKey  => $prosesId,
                        'produk_id'  => $produkId,
                        'qty'        => $toAdd,
                        'storage_id' => $storage->id,
                    ]);
                    
                    $remaining -= $toAdd;
                }
            }
        }

        if ($remaining > 0) {
            $storages = \App\Models\Storage::where('jenis', $isLiquid ? 'tangki' : 'gudang')
                ->when($preferredStorageId, function ($query) use ($preferredStorageId) {
                    return $query->where('id', '!=', $preferredStorageId);
                })
                ->get();
            
            $storagesWithSpace = $storages->map(function ($storage) {
                $currentStock = StokProduk::where('storage_id', $storage->id)->sum('qty');
                $capacity = (float) $storage->kapasitas;
                return [
                    'storage' => $storage,
                    'current_stock' => $currentStock,
                    'remaining_capacity' => max(0.0, $capacity - $currentStock),
                ];
            })->sortBy('current_stock');
            
            foreach ($storagesWithSpace as $item) {
                if ($remaining <= 0) break;
                
                $storage = $item['storage'];
                $space = $item['remaining_capacity'];
                
                if ($space > 0) {
                    $toAdd = min($remaining, $space);
                    $stok = StokProduk::firstOrCreate(
                        ['produk_id' => $produkId, 'storage_id' => $storage->id],
                        ['qty' => 0]
                    );
                    $stok->increment('qty', $toAdd);
                    
                    $modelClass::create([
                        $foreignKey  => $prosesId,
                        'produk_id'  => $produkId,
                        'qty'        => $toAdd,
                        'storage_id' => $storage->id,
                    ]);
                    
                    $remaining -= $toAdd;
                }
            }
        }

        if ($remaining > 0) {
            $fallbackStorageId = $preferredStorageId;
            if (!$fallbackStorageId) {
                $storages = \App\Models\Storage::where('jenis', $isLiquid ? 'tangki' : 'gudang')->get();
                $storagesWithSpace = $storages->map(function ($storage) {
                    return [
                        'storage_id' => $storage->id,
                        'current_stock' => StokProduk::where('storage_id', $storage->id)->sum('qty'),
                    ];
                })->sortBy('current_stock');
                $fallbackStorageId = $storagesWithSpace->first()['storage_id'] ?? null;
            }
            
            if ($fallbackStorageId) {
                $stok = StokProduk::firstOrCreate(
                    ['produk_id' => $produkId, 'storage_id' => $fallbackStorageId],
                    ['qty' => 0]
                );
                $stok->increment('qty', $remaining);
                
                $modelClass::create([
                    $foreignKey  => $prosesId,
                    'produk_id'  => $produkId,
                    'qty'        => $remaining,
                    'storage_id' => $fallbackStorageId,
                ]);
            }
        }
    }
}
