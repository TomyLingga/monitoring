<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index() { return Supplier::withCount('kontrakCpos')->latest()->get(); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string', 'alamat' => 'nullable|string',
            'telepon' => 'nullable|string', 'email' => 'nullable|email', 'pic' => 'nullable|string',
        ]);
        return Supplier::create($data);
    }

    public function show(Supplier $supplier) { return $supplier->load('kontrakCpos'); }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'nama' => 'required|string', 'alamat' => 'nullable|string',
            'telepon' => 'nullable|string', 'email' => 'nullable|email', 'pic' => 'nullable|string',
        ]);
        $supplier->update($data);
        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Supplier berhasil dihapus']);
    }
}
