<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KontrakPenjualan;
use Illuminate\Http\Request;

class KontrakPenjualanController extends Controller
{
    public function index() { return KontrakPenjualan::with(['buyer', 'produk'])->latest()->get(); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'buyer_id' => 'required|exists:buyers,id', 'produk_id' => 'required|exists:master_produks,id',
            'nomor_kontrak' => 'required|string|unique:kontrak_penjualans', 'qty' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0', 'tgl_kontrak' => 'nullable|date', 'tgl_jatuh_tempo' => 'nullable|date',
            'termin_pembayaran' => 'nullable|string', 'status' => 'nullable|in:aktif,selesai,batal',
        ]);
        return KontrakPenjualan::create($data)->load(['buyer', 'produk']);
    }
    public function show(KontrakPenjualan $kontrakPenjualan) { return $kontrakPenjualan->load(['buyer', 'produk', 'pengirimanPenjualans']); }
    public function update(Request $request, KontrakPenjualan $kontrakPenjualan)
    {
        $data = $request->validate([
            'buyer_id' => 'required|exists:buyers,id', 'produk_id' => 'required|exists:master_produks,id',
            'nomor_kontrak' => 'required|string|unique:kontrak_penjualans,nomor_kontrak,' . $kontrakPenjualan->id,
            'qty' => 'required|numeric|min:0', 'harga_satuan' => 'required|numeric|min:0',
            'tgl_kontrak' => 'nullable|date', 'tgl_jatuh_tempo' => 'nullable|date',
            'termin_pembayaran' => 'nullable|string', 'status' => 'nullable|in:aktif,selesai,batal',
        ]);
        $kontrakPenjualan->update($data);
        return $kontrakPenjualan->load(['buyer', 'produk']);
    }
    public function destroy(KontrakPenjualan $kontrakPenjualan) { $kontrakPenjualan->delete(); return response()->json(['message' => 'Kontrak penjualan berhasil dihapus']); }
}
