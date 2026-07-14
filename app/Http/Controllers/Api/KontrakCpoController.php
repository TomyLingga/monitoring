<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KontrakCpo;
use Illuminate\Http\Request;

class KontrakCpoController extends Controller
{
    public function index()
    {
        return KontrakCpo::with(['supplier', 'pembayaranCpos', 'incomingCpos'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'     => 'required|exists:suppliers,id',
            'nomor_kontrak'   => 'required|string|unique:kontrak_cpos',
            'jenis'           => 'nullable|in:lokal,impor',
            'mata_uang'       => 'nullable|in:IDR,USD',
            'qty'             => 'required|numeric|min:0',
            'harga_per_kg'    => 'required|numeric|min:0',
            'cbd_cad'         => 'nullable|string',
            'tgl_kontrak'     => 'nullable|date',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status'          => 'nullable|in:aktif,selesai,batal',
            'is_closed'       => 'nullable|boolean',
        ]);

        // Auto-set mata_uang based on jenis if not explicitly provided
        if (empty($data['mata_uang'])) {
            $data['mata_uang'] = ($data['jenis'] ?? 'lokal') === 'impor' ? 'USD' : 'IDR';
        }

        return KontrakCpo::create($data)->load('supplier');
    }

    public function show(KontrakCpo $kontrakCpo)
    {
        return $kontrakCpo->load(['supplier', 'incomingCpos', 'pembayaranCpos']);
    }

    public function update(Request $request, KontrakCpo $kontrakCpo)
    {
        $data = $request->validate([
            'supplier_id'     => 'required|exists:suppliers,id',
            'nomor_kontrak'   => 'required|string|unique:kontrak_cpos,nomor_kontrak,' . $kontrakCpo->id,
            'jenis'           => 'nullable|in:lokal,impor',
            'mata_uang'       => 'nullable|in:IDR,USD',
            'qty'             => 'required|numeric|min:0',
            'harga_per_kg'    => 'required|numeric|min:0',
            'cbd_cad'         => 'nullable|string',
            'tgl_kontrak'     => 'nullable|date',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status'          => 'nullable|in:aktif,selesai,batal',
            'is_closed'       => 'nullable|boolean',
        ]);

        // Auto-set mata_uang based on jenis if not explicitly provided
        if (empty($data['mata_uang'])) {
            $data['mata_uang'] = ($data['jenis'] ?? 'lokal') === 'impor' ? 'USD' : 'IDR';
        }

        $kontrakCpo->update($data);
        return $kontrakCpo->load('supplier');
    }

    public function destroy(KontrakCpo $kontrakCpo)
    {
        $kontrakCpo->delete();
        return response()->json(['message' => 'Kontrak CPO berhasil dihapus']);
    }

    /** Summary outstanding seluruh kontrak aktif */
    public function outstandingSummary()
    {
        $kontraks = KontrakCpo::where('is_closed', false)->with('supplier')->get();
        return response()->json([
            'data'    => $kontraks,
            'summary' => [
                'total_kontrak'           => $kontraks->count(),
                'total_outstanding_qty'   => round($kontraks->sum('outstanding_qty'), 2),
                'total_outstanding_nominal' => round($kontraks->sum('outstanding_nominal'), 2),
            ],
        ]);
    }
}
