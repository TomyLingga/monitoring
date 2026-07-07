<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranPenjualan;
use Illuminate\Http\Request;

class PembayaranPenjualanController extends Controller
{
    public function index()
    {
        return PembayaranPenjualan::with('kontrakPenjualan.buyer')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'nominal'              => 'required|numeric|min:0',
            'tgl_bayar'            => 'required|date',
            'catatan'              => 'nullable|string',
        ]);

        return PembayaranPenjualan::create($data)->load('kontrakPenjualan.buyer');
    }

    public function show($id)
    {
        return PembayaranPenjualan::with('kontrakPenjualan.buyer')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPenjualan::findOrFail($id);
        $data = $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'nominal'              => 'required|numeric|min:0',
            'tgl_bayar'            => 'required|date',
            'catatan'              => 'nullable|string',
        ]);

        $pembayaran->update($data);
        return $pembayaran->load('kontrakPenjualan.buyer');
    }

    public function destroy($id)
    {
        $pembayaran = PembayaranPenjualan::findOrFail($id);
        $pembayaran->delete();
        return response()->json(['message' => 'Pembayaran penjualan berhasil dihapus']);
    }
}
