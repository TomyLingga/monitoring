<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{AlokasiStok, PengirimanPenjualan};
use Illuminate\Http\Request;

class AlokasiStokController extends Controller
{
    public function index()
    {
        return AlokasiStok::with(['pengiriman.kontrakPenjualan.buyer','storage','produk'])->latest()->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'pengiriman_id'  => 'required|exists:pengiriman_penjualans,id',
            'storage_id'     => 'required|exists:storages,id',
            'produk_id'      => 'nullable|exists:master_produks,id',
            'qty_alokasi'    => 'required|numeric|min:0',
            'tgl_alokasi'    => 'nullable|date',
            'keterangan'     => 'nullable|string',
        ]);
        if (empty($data['tgl_alokasi'])) $data['tgl_alokasi'] = date('Y-m-d');
        $a = AlokasiStok::create($data);

        // Auto-recalculate pengiriman qty_alokasi
        $p = PengirimanPenjualan::find($data['pengiriman_id']);
        if ($p) {
            $p->recalculateAlokasi();
            if ($p->qty_alokasi >= $p->qty_order && $p->status === 'draft') {
                $p->status = 'allocated';
                $p->save();
            }
        }
        return $a->load(['pengiriman.kontrakPenjualan','storage','produk']);
    }

    public function update(Request $r, AlokasiStok $alokasiStok)
    {
        $data = $r->validate([
            'pengiriman_id'  => 'required|exists:pengiriman_penjualans,id',
            'storage_id'     => 'required|exists:storages,id',
            'produk_id'      => 'nullable|exists:master_produks,id',
            'qty_alokasi'    => 'required|numeric|min:0',
            'tgl_alokasi'    => 'nullable|date',
            'keterangan'     => 'nullable|string',
        ]);
        $alokasiStok->update($data);

        $p = PengirimanPenjualan::find($alokasiStok->pengiriman_id);
        if ($p) $p->recalculateAlokasi();

        return $alokasiStok->load(['pengiriman.kontrakPenjualan','storage','produk']);
    }

    public function destroy(AlokasiStok $alokasiStok)
    {
        $pid = $alokasiStok->pengiriman_id;
        $alokasiStok->delete();

        $p = PengirimanPenjualan::find($pid);
        if ($p) $p->recalculateAlokasi();

        return response()->json(['message'=>'OK']);
    }
}
