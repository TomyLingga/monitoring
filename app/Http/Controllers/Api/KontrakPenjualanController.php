<?php
namespace App\Http\Controllers\Api;

// Re-export from MarketingPnDController.php — classes already defined there
// We just need individual autoloadable files, so include them here
// Note: PHP cannot redeclare, so we rely on the main file being autoloaded via PSR-4

// This is a proxy file — KontrakPenjualanController is defined in MarketingPnDController.php
// But we need this file to exist for PSR-4 autoloading compatibility.
// The real solution: ensure the class is defined here directly.

use App\Http\Controllers\Controller;
use App\Models\KontrakPenjualan;
use Illuminate\Http\Request;

class KontrakPenjualanController extends Controller
{
    public function index() {
        return KontrakPenjualan::with(['buyer','produk','pengirimanPenjualans','invoices'])->latest()->get();
    }
    public function store(Request $r) {
        $data = $r->validate(['buyer_id'=>'required|exists:buyers,id','produk_id'=>'required|exists:master_produks,id','nomor_kontrak'=>'required|string|unique:kontrak_penjualans','jenis'=>'required|in:lokal,ekspor','mata_uang'=>'required|in:IDR,USD','qty'=>'required|numeric|min:0','harga_satuan'=>'required|numeric|min:0','kurs_konversi'=>'nullable|numeric|min:0','incoterm'=>'nullable|string','levy_rate_usd'=>'nullable|numeric|min:0','termin_pembayaran'=>'nullable|string','metode_invoice'=>'nullable|in:invoice,kontrak','tgl_kontrak'=>'nullable|date','tgl_jatuh_tempo'=>'nullable|date','status'=>'nullable|in:aktif,selesai,batal','keterangan'=>'nullable|string']);
        if ($data['jenis']==='ekspor') $data['mata_uang']='USD';
        if ($data['jenis']==='lokal')  $data['mata_uang']='IDR';
        return KontrakPenjualan::create($data)->load(['buyer','produk']);
    }
    public function show(KontrakPenjualan $k) { return $k->load(['buyer','produk','pengirimanPenjualans.jadwalKapal','invoices','pembayaranPenjualans']); }
    public function update(Request $r, KontrakPenjualan $kontrakPenjualan) {
        $data = $r->validate(['buyer_id'=>'required|exists:buyers,id','produk_id'=>'required|exists:master_produks,id','nomor_kontrak'=>'required|string|unique:kontrak_penjualans,nomor_kontrak,'.$kontrakPenjualan->id,'jenis'=>'required|in:lokal,ekspor','mata_uang'=>'required|in:IDR,USD','qty'=>'required|numeric|min:0','harga_satuan'=>'required|numeric|min:0','kurs_konversi'=>'nullable|numeric|min:0','incoterm'=>'nullable|string','levy_rate_usd'=>'nullable|numeric|min:0','termin_pembayaran'=>'nullable|string','metode_invoice'=>'nullable|in:invoice,kontrak','tgl_kontrak'=>'nullable|date','tgl_jatuh_tempo'=>'nullable|date','status'=>'nullable|in:aktif,selesai,batal','keterangan'=>'nullable|string']);
        if ($data['jenis']==='ekspor') $data['mata_uang']='USD';
        if ($data['jenis']==='lokal')  $data['mata_uang']='IDR';
        $kontrakPenjualan->update($data);
        return $kontrakPenjualan->load(['buyer','produk','pengirimanPenjualans','invoices']);
    }
    public function destroy(KontrakPenjualan $k) { $k->delete(); return response()->json(['message'=>'OK']); }
}
