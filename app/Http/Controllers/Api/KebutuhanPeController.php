<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KebutuhanPe;
use Illuminate\Http\Request;

class KebutuhanPeController extends Controller {
    public function index(Request $r) {
        $q = KebutuhanPe::with('penguranganPes');
        if ($r->tgl_start) $q->whereDate('tgl','>=',$r->tgl_start);
        if ($r->tgl_end)   $q->whereDate('tgl','<=',$r->tgl_end);
        return $q->orderBy('tgl')->get()->map(fn($k)=>array_merge($k->toArray(),['total_dikurangi'=>(float)$k->penguranganPes->sum('qty'),'outstanding'=>$k->outstanding]));
    }
    public function store(Request $r) { return KebutuhanPe::create($r->validate(['tgl'=>'required|date','periode'=>'nullable|string','qty_kebutuhan'=>'required|numeric|min:0','qty_realisasi'=>'nullable|numeric|min:0','keterangan'=>'nullable|string']))->load('penguranganPes'); }
    public function show(KebutuhanPe $k) { return $k->load('penguranganPes'); }
    public function update(Request $r, KebutuhanPe $kebutuhanPe) { $kebutuhanPe->update($r->validate(['tgl'=>'required|date','periode'=>'nullable|string','qty_kebutuhan'=>'required|numeric|min:0','qty_realisasi'=>'nullable|numeric|min:0','keterangan'=>'nullable|string'])); return $kebutuhanPe->load('penguranganPes'); }
    public function destroy(KebutuhanPe $k) { $k->delete(); return response()->json(['message'=>'OK']); }
}
