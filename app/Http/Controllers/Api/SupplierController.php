<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
class SupplierController extends Controller {
    public function index() { return Supplier::latest()->get(); }
    public function store(Request $r) { return Supplier::create($r->validate(['nama'=>'required','kode'=>'nullable','alamat'=>'nullable','kontak'=>'nullable','keterangan'=>'nullable'])); }
    public function show(Supplier $s) { return $s; }
    public function update(Request $r, Supplier $supplier) { $supplier->update($r->validate(['nama'=>'required','kode'=>'nullable','alamat'=>'nullable','kontak'=>'nullable','keterangan'=>'nullable'])); return $supplier; }
    public function destroy(Supplier $supplier) { $supplier->delete(); return response()->json(['message'=>'OK']); }
}
