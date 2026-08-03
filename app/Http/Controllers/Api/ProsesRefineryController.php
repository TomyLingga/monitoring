<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{ProsesRefinery, BahanRefinery, HasilRefinery};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesRefineryController extends Controller
{
    public function index() { return ProsesRefinery::with(['storage','bahans.produk','hasils.produk'])->latest('tgl_proses')->get(); }
    public function store(Request $request) {
        $data = $request->validate(['storage_id'=>'nullable|exists:storages,id','tgl_proses'=>'required|date','keterangan'=>'nullable|string','bahans'=>'required|array','bahans.*.produk_id'=>'required|exists:master_produks,id','bahans.*.storage_id'=>'nullable|exists:storages,id','bahans.*.qty'=>'required|numeric|min:0','hasils'=>'required|array','hasils.*.produk_id'=>'required|exists:master_produks,id','hasils.*.storage_id'=>'nullable|exists:storages,id','hasils.*.qty'=>'required|numeric|min:0']);
        DB::beginTransaction();
        try {
            $proses = ProsesRefinery::create(['storage_id'=>$data['storage_id']??null,'tgl_proses'=>$data['tgl_proses'],'total_bahan'=>collect($data['bahans'])->sum('qty'),'total_hasil'=>collect($data['hasils'])->sum('qty'),'losses'=>collect($data['bahans'])->sum('qty')-collect($data['hasils'])->sum('qty'),'keterangan'=>$data['keterangan']??null]);
            foreach ($data['bahans'] as $b) BahanRefinery::create(['proses_refinery_id'=>$proses->id]+$b);
            foreach ($data['hasils'] as $h) HasilRefinery::create(['proses_refinery_id'=>$proses->id]+$h);
            DB::commit();
            return $proses->load(['storage','bahans.produk','hasils.produk']);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }
    public function show(ProsesRefinery $prosesRefinery) { return $prosesRefinery->load(['storage','bahans.produk','hasils.produk']); }
    public function update(Request $request, ProsesRefinery $prosesRefinery) {
        $data = $request->validate(['storage_id'=>'nullable|exists:storages,id','tgl_proses'=>'required|date','keterangan'=>'nullable|string','bahans'=>'nullable|array','bahans.*.produk_id'=>'required_with:bahans|exists:master_produks,id','bahans.*.storage_id'=>'nullable|exists:storages,id','bahans.*.qty'=>'required_with:bahans|numeric|min:0','hasils'=>'nullable|array','hasils.*.produk_id'=>'required_with:hasils|exists:master_produks,id','hasils.*.storage_id'=>'nullable|exists:storages,id','hasils.*.qty'=>'required_with:hasils|numeric|min:0']);
        DB::beginTransaction();
        try {
            if (!empty($data['bahans'])) { $prosesRefinery->bahans()->delete(); foreach ($data['bahans'] as $b) BahanRefinery::create(['proses_refinery_id'=>$prosesRefinery->id]+$b); }
            if (!empty($data['hasils'])) { $prosesRefinery->hasils()->delete(); foreach ($data['hasils'] as $h) HasilRefinery::create(['proses_refinery_id'=>$prosesRefinery->id]+$h); }
            $prosesRefinery->update(['storage_id'=>$data['storage_id']??$prosesRefinery->storage_id,'tgl_proses'=>$data['tgl_proses'],'total_bahan'=>!empty($data['bahans'])?collect($data['bahans'])->sum('qty'):$prosesRefinery->total_bahan,'total_hasil'=>!empty($data['hasils'])?collect($data['hasils'])->sum('qty'):$prosesRefinery->total_hasil,'losses'=>(!empty($data['bahans'])?collect($data['bahans'])->sum('qty'):$prosesRefinery->total_bahan)-(!empty($data['hasils'])?collect($data['hasils'])->sum('qty'):$prosesRefinery->total_hasil),'keterangan'=>$data['keterangan']??$prosesRefinery->keterangan]);
            DB::commit();
            return $prosesRefinery->load(['storage','bahans.produk','hasils.produk']);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message'=>$e->getMessage()],500); }
    }
    public function destroy(ProsesRefinery $prosesRefinery) { $prosesRefinery->delete(); return response()->json(['message'=>'OK']); }
}
