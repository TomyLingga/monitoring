<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StokProduk;
use Illuminate\Http\Request;

class StokProdukController extends Controller
{
    public function index()
    {
        return StokProduk::with(['storage','produk'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'storage_id'   => 'required|exists:storages,id',
            'produk_id'    => 'required|exists:master_produks,id',
            'qty'          => 'required|numeric|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'mata_uang'    => 'nullable|in:IDR,USD',
            'tgl_update'   => 'nullable|date',
            'keterangan'   => 'nullable|string',
        ]);
        $stok = StokProduk::updateOrCreate(
            ['storage_id' => $data['storage_id'], 'produk_id' => $data['produk_id']],
            $data
        );
        return $stok->load(['storage','produk']);
    }

    public function update(Request $request, StokProduk $stokProduk)
    {
        $data = $request->validate([
            'qty'          => 'required|numeric|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'mata_uang'    => 'nullable|in:IDR,USD',
            'tgl_update'   => 'nullable|date',
            'keterangan'   => 'nullable|string',
        ]);
        $stokProduk->update($data);
        return $stokProduk->load(['storage','produk']);
    }

    public function destroy(StokProduk $stokProduk)
    {
        $stokProduk->delete();
        return response()->json(['message' => 'Stok dihapus']);
    }
}
