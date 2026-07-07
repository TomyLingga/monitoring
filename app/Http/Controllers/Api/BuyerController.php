<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index() { return Buyer::withCount('kontrakPenjualans')->latest()->get(); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string', 'alamat' => 'nullable|string', 
            'telepon' => 'nullable|string', 'email' => 'nullable|email', 'pic' => 'nullable|string',
            'keterangan' => 'nullable|string|in:lokal,ekspor'
        ]);
        return Buyer::create($data);
    }
    public function show(Buyer $buyer) { return $buyer->load('kontrakPenjualans'); }
    public function update(Request $request, Buyer $buyer)
    {
        $data = $request->validate([
            'nama' => 'required|string', 'alamat' => 'nullable|string', 
            'telepon' => 'nullable|string', 'email' => 'nullable|email', 'pic' => 'nullable|string',
            'keterangan' => 'nullable|string|in:lokal,ekspor'
        ]);
        $buyer->update($data);
        return $buyer;
    }
    public function destroy(Buyer $buyer) { $buyer->delete(); return response()->json(['message' => 'Buyer berhasil dihapus']); }
}
