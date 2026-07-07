<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StokProduk;
use Illuminate\Http\Request;

class StokProdukController extends Controller
{
    public function index()
    {
        return response()->json(StokProduk::with(['produk', 'storage'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id'  => 'required|exists:master_produks,id',
            'storage_id' => 'required|exists:storages,id',
            'qty'        => 'required|numeric|min:0',
        ]);

        $stok = StokProduk::updateOrCreate(
            [
                'produk_id'  => $request->produk_id,
                'storage_id' => $request->storage_id,
            ],
            [
                'qty' => $request->qty,
            ]
        );

        return response()->json($stok->load(['produk', 'storage']), 201);
    }

    public function show($id)
    {
        return response()->json(StokProduk::with(['produk', 'storage'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id'  => 'nullable|exists:master_produks,id',
            'storage_id' => 'nullable|exists:storages,id',
            'qty'        => 'required|numeric|min:0',
        ]);

        $stok = StokProduk::findOrFail($id);
        $stok->update($request->only(['produk_id', 'storage_id', 'qty']));

        return response()->json($stok->load(['produk', 'storage']));
    }

    public function destroy($id)
    {
        StokProduk::findOrFail($id)->delete();
        return response()->json(['message' => 'Stok produk berhasil dihapus']);
    }
}
