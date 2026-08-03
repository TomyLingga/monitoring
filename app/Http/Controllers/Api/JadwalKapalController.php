<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalKapal;
use Illuminate\Http\Request;

class JadwalKapalController extends Controller
{
    public function index()
    {
        return JadwalKapal::with(['pengirimanPenjualans.kontrakPenjualan.buyer'])->orderBy('laycan_start')->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nama_kapal'    => 'required|string',
            'nomor_voyage'  => 'nullable|string',
            'bendera'       => 'nullable|string',
            'laycan_start'  => 'nullable|date',
            'laycan_end'    => 'nullable|date',
            'eta'           => 'nullable|date',
            'etb'           => 'nullable|date',
            'etd'           => 'nullable|date',
            'port_muat'     => 'nullable|string',
            'port_bongkar'  => 'nullable|string',
            'target_qty'    => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:scheduled,loading,departed,arrived,done',
            'keterangan'    => 'nullable|string',
        ]);
        return JadwalKapal::create($data)->load(['pengirimanPenjualans.kontrakPenjualan.buyer']);
    }

    public function show(JadwalKapal $jadwalKapal)
    {
        return $jadwalKapal->load(['pengirimanPenjualans.kontrakPenjualan.buyer']);
    }

    public function update(Request $r, JadwalKapal $jadwalKapal)
    {
        $data = $r->validate([
            'nama_kapal'    => 'required|string',
            'nomor_voyage'  => 'nullable|string',
            'bendera'       => 'nullable|string',
            'laycan_start'  => 'nullable|date',
            'laycan_end'    => 'nullable|date',
            'eta'           => 'nullable|date',
            'etb'           => 'nullable|date',
            'etd'           => 'nullable|date',
            'port_muat'     => 'nullable|string',
            'port_bongkar'  => 'nullable|string',
            'target_qty'    => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:scheduled,loading,departed,arrived,done',
            'keterangan'    => 'nullable|string',
        ]);
        $jadwalKapal->update($data);
        return $jadwalKapal->load(['pengirimanPenjualans.kontrakPenjualan.buyer']);
    }

    public function destroy(JadwalKapal $jadwalKapal)
    {
        $jadwalKapal->delete();
        return response()->json(['message' => 'OK']);
    }
}
