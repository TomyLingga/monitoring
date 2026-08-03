<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengirimanPenjualan;
use Illuminate\Http\Request;

class PengirimanPenjualanController extends Controller
{
    private $with = [
        'kontrakPenjualan.buyer','kontrakPenjualan.produk',
        'jadwalKapal','alokasis.storage','alokasis.produk',
        'kebutuhanProduksis','truckings','invoices','kebutuhanCpoDos'
    ];

    public function index(Request $r)
    {
        $q = PengirimanPenjualan::with($this->with)->latest();
        if ($r->kontrak_id) $q->where('kontrak_penjualan_id', $r->kontrak_id);
        if ($r->status) $q->where('status', $r->status);
        return $q->get()->map(fn($p) => array_merge($p->toArray(), [
            'kekurangan_stok' => $p->kekurangan_stok,
            'kekurangan_cpo'  => $p->kekurangan_cpo,
        ]));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'jadwal_kapal_id'      => 'nullable|exists:jadwal_kapals,id',
            'nomor_do'             => 'nullable|string',
            'qty_order'            => 'required|numeric|min:0',
            'qty_kirim'            => 'nullable|numeric|min:0',
            'qty_terima'           => 'nullable|numeric|min:0',
            'tgl_rencana'          => 'nullable|date',
            'tgl_realisasi'        => 'nullable|date',
            'laycan_start'         => 'nullable|date',
            'laycan_end'           => 'nullable|date',
            'via'                  => 'nullable|in:kapal,truk,lokal',
            'status'               => 'nullable|in:draft,allocated,scheduled,loading,shipped,done,cancelled',
            'termin'               => 'nullable|string',
            'keterangan'           => 'nullable|string',
        ]);

        // Auto-generate nomor DO
        if (empty($data['nomor_do'])) {
            $data['nomor_do'] = 'DO-' . date('Ymd') . '-' . rand(100, 999);
        }

        // qty_alokasi starts at 0 (auto-calculated from alokasi_stoks)
        $data['qty_alokasi'] = 0;

        $p = PengirimanPenjualan::create($data);
        return $p->load($this->with)->append(['kekurangan_stok', 'kekurangan_cpo']);
    }

    public function show(PengirimanPenjualan $pengirimanPenjualan)
    {
        return $pengirimanPenjualan->load($this->with)
            ->append(['kekurangan_stok', 'kekurangan_cpo']);
    }

    public function update(Request $r, PengirimanPenjualan $pengirimanPenjualan)
    {
        $data = $r->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'jadwal_kapal_id'      => 'nullable|exists:jadwal_kapals,id',
            'nomor_do'             => 'nullable|string',
            'qty_order'            => 'required|numeric|min:0',
            'qty_kirim'            => 'nullable|numeric|min:0',
            'qty_terima'           => 'nullable|numeric|min:0',
            'tgl_rencana'          => 'nullable|date',
            'tgl_realisasi'        => 'nullable|date',
            'laycan_start'         => 'nullable|date',
            'laycan_end'           => 'nullable|date',
            'via'                  => 'nullable|in:kapal,truk,lokal',
            'status'               => 'nullable|in:draft,allocated,scheduled,loading,shipped,done,cancelled',
            'termin'               => 'nullable|string',
            'keterangan'           => 'nullable|string',
        ]);

        // Don't allow manual qty_alokasi — auto-calculated
        unset($data['qty_alokasi']);

        $pengirimanPenjualan->update($data);
        $pengirimanPenjualan->recalculateAlokasi();

        return $pengirimanPenjualan->load($this->with)
            ->append(['kekurangan_stok', 'kekurangan_cpo']);
    }

    public function destroy(PengirimanPenjualan $pengirimanPenjualan)
    {
        $pengirimanPenjualan->delete();
        return response()->json(['message' => 'OK']);
    }
}
