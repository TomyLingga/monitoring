<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PenguranganPe;
use Illuminate\Http\Request;

class PenguranganPeController extends Controller {
    public function store(Request $r) { return PenguranganPe::create($r->validate(['kebutuhan_pe_id'=>'nullable|exists:kebutuhan_pes,id','tgl'=>'required|date','qty'=>'required|numeric|min:0','keterangan'=>'nullable|string']))->load('kebutuhanPe'); }
    public function destroy(PenguranganPe $p) { $p->delete(); return response()->json(['message'=>'OK']); }
}
