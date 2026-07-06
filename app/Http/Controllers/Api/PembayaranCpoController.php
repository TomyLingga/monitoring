<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PembayaranCpo;
use Illuminate\Http\Request;

class PembayaranCpoController extends Controller
{
    public function index() { return PembayaranCpo::with('kontrakCpo.supplier')->latest('tgl_bayar')->get(); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'nominal' => 'required|numeric|min:0', 'tgl_bayar' => 'required|date',
            'metode_bayar' => 'nullable|string', 'bukti_bayar' => 'nullable|string', 'catatan' => 'nullable|string',
        ]);
        return PembayaranCpo::create($data)->load('kontrakCpo');
    }
    public function show(PembayaranCpo $pembayaranCpo) { return $pembayaranCpo->load('kontrakCpo.supplier'); }
    public function update(Request $request, PembayaranCpo $pembayaranCpo)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'nominal' => 'required|numeric|min:0', 'tgl_bayar' => 'required|date',
            'metode_bayar' => 'nullable|string', 'bukti_bayar' => 'nullable|string', 'catatan' => 'nullable|string',
        ]);
        $pembayaranCpo->update($data);
        return $pembayaranCpo->load('kontrakCpo');
    }
    public function destroy(PembayaranCpo $pembayaranCpo) { $pembayaranCpo->delete(); return response()->json(['message' => 'Pembayaran berhasil dihapus']); }
}
