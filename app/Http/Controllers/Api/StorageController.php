<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Storage as StorageModel;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        return StorageModel::with('stokProduks.produk')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'lokasi' => 'nullable|string',
            'kapasitas' => 'required|numeric|min:0',
            'jenis' => 'required|in:tangki,gudang',
        ]);
        return StorageModel::create($data);
    }

    public function show(StorageModel $storage)
    {
        return $storage->load('stokProduks.produk');
    }

    public function update(Request $request, StorageModel $storage)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'lokasi' => 'nullable|string',
            'kapasitas' => 'required|numeric|min:0',
            'jenis' => 'required|in:tangki,gudang',
        ]);
        $storage->update($data);
        return $storage;
    }

    public function destroy(StorageModel $storage)
    {
        $storage->delete();
        return response()->json(['message' => 'Storage berhasil dihapus']);
    }
}
