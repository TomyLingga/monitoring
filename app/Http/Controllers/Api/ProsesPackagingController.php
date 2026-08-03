<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{ProsesPackaging, BahanPackaging, HasilPackaging};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesPackagingController extends Controller
{
    public function index() { return ProsesPackaging::with(['storage','bahans.produk','hasils.produk'])->latest('tgl_proses')->get(); }
    public function store(Request $request) {
        $data = $request->validate(['storage_id'=>'nullable|exists:storages,id','tgl_proses'=>'required|date','keterangan'=>'nullable','bahans'=>'required|array','bahans.*.produk_id'=>'required|exists:master_produks,id','bahans.*.storage_id'=>'nullable|exists:storages,id','bahans.*.qty'=>'required|numeric','hasils'=>'required|array','hasils.*.produk_id'=>'required|exists:master_produks,id','hasils.*.storage_id'=>'nullable|exists:storages,id','hasils.*.qty'=>'required|numeric']);
        DB::beginTransaction();
        try {
            $p = ProsesPackaging::create(['storage_id'=>$data['storage_id']??null,'tgl_proses'=>$data['tgl_proses'],'total_bahan'=>collect($data['bahans'])->sum('qty'),'total_hasil'=>collect($data['hasils'])->sum('qty'),'losses'=>collect($data['bahans'])->sum('qty')-collect($data['hasils'])->sum('qty'),'keterangan'=>$data['keterangan']??null]);
            foreach ($data['bahans'] as $b) BahanPackaging::create(['proses_packaging_id'=>$p->id]+$b);
            foreach ($data['hasils'] as $h) HasilPackaging::create(['proses_packaging_id'=>$p->id]+$h);
            DB::commit();
            return $p->load(['storage','bahans.produk','hasils.produk']);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }
    public function show(ProsesPackaging $prosesPackaging) { return $prosesPackaging->load(['storage','bahans.produk','hasils.produk']); }
    public function update(Request $r, ProsesPackaging $prosesPackaging) { return $prosesPackaging->load(['storage','bahans.produk','hasils.produk']); }
    public function destroy(ProsesPackaging $prosesPackaging) { $prosesPackaging->delete(); return response()->json(['message'=>'OK']); }
}
