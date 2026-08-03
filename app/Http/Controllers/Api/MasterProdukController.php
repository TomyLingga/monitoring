<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
class MasterProdukController extends Controller {
    public function index() { return MasterProduk::latest()->get(); }
    public function store(Request $r) {
        return MasterProduk::create($r->validate(['nama_produk'=>'required','kode_produk'=>'required|unique:master_produks,kode_produk','kategori'=>'nullable|in:CPO,Olein,Stearin,PFAD,RBDPO,PKO,Lainnya','satuan'=>'nullable','yield_dari_cpo'=>'nullable|numeric','keterangan'=>'nullable']));
    }
    public function show(MasterProduk $masterProduk) { return $masterProduk; }
    public function update(Request $r, MasterProduk $masterProduk) {
        $masterProduk->update($r->validate(['nama_produk'=>'required','kode_produk'=>'required|unique:master_produks,kode_produk,'.$masterProduk->id,'kategori'=>'nullable|in:CPO,Olein,Stearin,PFAD,RBDPO,PKO,Lainnya','satuan'=>'nullable','yield_dari_cpo'=>'nullable|numeric','keterangan'=>'nullable']));
        return $masterProduk;
    }
    public function destroy(MasterProduk $masterProduk) { $masterProduk->delete(); return response()->json(['message'=>'OK']); }
}
