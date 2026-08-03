<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;
class BuyerController extends Controller {
    public function index() { return Buyer::latest()->get(); }
    public function store(Request $r) { return Buyer::create($r->validate(['nama'=>'required','kode'=>'nullable','negara'=>'nullable','kontak'=>'nullable','keterangan'=>'nullable'])); }
    public function show(Buyer $b) { return $b; }
    public function update(Request $r, Buyer $buyer) { $buyer->update($r->validate(['nama'=>'required','kode'=>'nullable','negara'=>'nullable','kontak'=>'nullable','keterangan'=>'nullable'])); return $buyer; }
    public function destroy(Buyer $buyer) { $buyer->delete(); return response()->json(['message'=>'OK']); }
}
