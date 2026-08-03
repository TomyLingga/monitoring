<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Trucking;
use Illuminate\Http\Request;

class TruckingController extends Controller {
    public function index(Request $r) {
        $q = Trucking::with(['pengiriman.kontrakPenjualan.buyer','produk']);
        if ($r->tgl_start) $q->whereDate('tgl','>=',$r->tgl_start);
        if ($r->tgl_end)   $q->whereDate('tgl','<=',$r->tgl_end);
        $truckings = $q->orderBy('tgl')->get();
        $grouped = $truckings->groupBy(fn($t)=>$t->tgl->format('Y-m-d'))->map(function($dg,$tgl) {
            $byT = $dg->groupBy('transporter_name')->map(fn($tg,$n)=>['transporter_name'=>$n,'destinations'=>$tg->pluck('destination')->unique()->filter()->values(),'qty_unit'=>$tg->sum('qty_unit'),'qty_produk'=>(float)$tg->sum('qty_produk'),'records'=>$tg->values()])->values();
            return ['tgl'=>$tgl,'by_transporter'=>$byT,'grand_total_unit'=>$dg->sum('qty_unit'),'grand_total_qty'=>(float)$dg->sum('qty_produk')];
        })->values();
        return response()->json(['records'=>$truckings,'summary'=>$grouped]);
    }
    public function store(Request $r) { return Trucking::create($r->validate(['pengiriman_id'=>'nullable|exists:pengiriman_penjualans,id','tgl'=>'required|date','transporter_name'=>'required|string','destination'=>'nullable|string','qty_unit'=>'required|integer|min:1','qty_produk'=>'required|numeric|min:0','produk_id'=>'nullable|exists:master_produks,id','no_polisi'=>'nullable|string','no_surat_jalan'=>'nullable|string','keterangan'=>'nullable|string']))->load(['pengiriman.kontrakPenjualan.buyer','produk']); }
    public function show(Trucking $t) { return $t->load(['pengiriman.kontrakPenjualan.buyer','produk']); }
    public function update(Request $r, Trucking $trucking) { $trucking->update($r->validate(['pengiriman_id'=>'nullable|exists:pengiriman_penjualans,id','tgl'=>'required|date','transporter_name'=>'required|string','destination'=>'nullable|string','qty_unit'=>'required|integer|min:1','qty_produk'=>'required|numeric|min:0','produk_id'=>'nullable|exists:master_produks,id','no_polisi'=>'nullable|string','no_surat_jalan'=>'nullable|string','keterangan'=>'nullable|string'])); return $trucking->load(['pengiriman.kontrakPenjualan.buyer','produk']); }
    public function destroy(Trucking $t) { $t->delete(); return response()->json(['message'=>'OK']); }
}
