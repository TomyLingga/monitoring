<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Storage;
use Illuminate\Http\Request;
class StorageController extends Controller {
    public function index() { return Storage::latest()->get(); }
    public function store(Request $r) { return Storage::create($r->validate(['nama'=>'required','kode'=>'nullable','tipe'=>'nullable|in:tangki,gudang,silo,lainnya','kapasitas'=>'nullable|numeric','lokasi'=>'nullable','keterangan'=>'nullable'])); }
    public function show(Storage $s) { return $s; }
    public function update(Request $r, Storage $storage) { $storage->update($r->validate(['nama'=>'required','kode'=>'nullable','tipe'=>'nullable|in:tangki,gudang,silo,lainnya','kapasitas'=>'nullable|numeric','lokasi'=>'nullable','keterangan'=>'nullable'])); return $storage; }
    public function destroy(Storage $storage) { $storage->delete(); return response()->json(['message'=>'OK']); }
}
