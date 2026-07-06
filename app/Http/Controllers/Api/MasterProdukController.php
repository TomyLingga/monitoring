<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterProduk;
use Illuminate\Http\Request;

class MasterProdukController extends Controller
{
    public function index()
    {
        return MasterProduk::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_produk' => 'required|string',
            'satuan' => 'required|string',
            'kode_produk' => 'required|string|unique:master_produks',
        ]);
        return MasterProduk::create($data);
    }

    public function show(MasterProduk $masterProduk)
    {
        return $masterProduk->load('stokProduks.storage');
    }

    public function update(Request $request, MasterProduk $masterProduk)
    {
        $data = $request->validate([
            'nama_produk' => 'required|string',
            'satuan' => 'required|string',
            'kode_produk' => 'required|string|unique:master_produks,kode_produk,' . $masterProduk->id,
        ]);
        $masterProduk->update($data);
        return $masterProduk;
    }

    public function destroy(MasterProduk $masterProduk)
    {
        $masterProduk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
