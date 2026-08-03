<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KontrakCpo;
use Illuminate\Http\Request;

class KontrakCpoController extends Controller
{
    public function index()
    {
        return KontrakCpo::with(['supplier','incomingCpos','pembayaranCpos'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'nomor_kontrak'     => 'required|string|unique:kontrak_cpos',
            'jenis'             => 'required|in:lokal,impor',
            'mata_uang'         => 'required|in:IDR,USD',
            'qty'               => 'required|numeric|min:0',
            'harga_per_kg'      => 'required|numeric|min:0',
            'termin_pembayaran' => 'nullable|string',
            'tgl_kontrak'       => 'nullable|date',
            'tgl_jatuh_tempo'   => 'nullable|date',
            'status'            => 'nullable|in:aktif,selesai,batal',
            'keterangan'        => 'nullable|string',
        ]);
        return KontrakCpo::create($data)->load(['supplier']);
    }

    public function show(KontrakCpo $kontrakCpo)
    {
        return $kontrakCpo->load(['supplier','incomingCpos.storage','pembayaranCpos']);
    }

    public function update(Request $request, KontrakCpo $kontrakCpo)
    {
        $data = $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'nomor_kontrak'     => 'required|string|unique:kontrak_cpos,nomor_kontrak,'.$kontrakCpo->id,
            'jenis'             => 'required|in:lokal,impor',
            'mata_uang'         => 'required|in:IDR,USD',
            'qty'               => 'required|numeric|min:0',
            'harga_per_kg'      => 'required|numeric|min:0',
            'termin_pembayaran' => 'nullable|string',
            'tgl_kontrak'       => 'nullable|date',
            'tgl_jatuh_tempo'   => 'nullable|date',
            'status'            => 'nullable|in:aktif,selesai,batal',
            'keterangan'        => 'nullable|string',
        ]);
        $kontrakCpo->update($data);
        return $kontrakCpo->load(['supplier','incomingCpos','pembayaranCpos']);
    }

    public function destroy(KontrakCpo $kontrakCpo)
    {
        $kontrakCpo->delete();
        return response()->json(['message' => 'Kontrak CPO dihapus']);
    }
}
