<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{KontrakPenjualan, PengirimanPenjualan, IncomingCpo, StokProduk, JadwalKapal, KebutuhanProduksi, BankAccount};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Pemenuhan Sales — per pengiriman aktif dengan info lengkap
        $pengirimanAktif = PengirimanPenjualan::with([
            'kontrakPenjualan.buyer',
            'kontrakPenjualan.produk',
            'jadwalKapal',
            'alokasis.storage',
            'kebutuhanProduksis',
        ])
        ->whereIn('status', ['draft','allocated','scheduled','loading'])
        ->orderBy('tgl_rencana')
        ->get()
        ->map(function ($p) {
            $kontrak = $p->kontrakPenjualan;
            $produk  = $kontrak?->produk;
            $yield   = (float)($produk?->yield_dari_cpo ?? 0.82);
            $qtyOrder = (float)$p->qty_order;
            $qtyAlokasi = (float)($p->qty_alokasi ?? 0);
            $kekuranganStok = max(0, $qtyOrder - $qtyAlokasi);
            $kekuranganCpo  = $yield > 0 ? $kekuranganStok / $yield : 0;

            // Kebutuhan produksi
            $kebutuhan = $p->kebutuhanProduksis->first();

            return [
                'id'             => $p->id,
                'nomor_do'       => $p->nomor_do,
                'buyer'          => $kontrak?->buyer?->nama,
                'produk'         => $produk?->nama_produk,
                'jenis'          => $kontrak?->jenis,
                'qty_order'      => $qtyOrder,
                'qty_alokasi'    => $qtyAlokasi,
                'kekurangan_stok'=> $kekuranganStok,
                'kekurangan_cpo' => round($kekuranganCpo, 2),
                'via'            => $p->via,
                'nama_kapal'     => $p->jadwalKapal?->nama_kapal,
                'laycan_start'   => $p->laycan_start ?? $p->jadwalKapal?->laycan_start,
                'laycan_end'     => $p->laycan_end ?? $p->jadwalKapal?->laycan_end,
                'eta'            => $p->jadwalKapal?->eta,
                'tgl_rencana'    => $p->tgl_rencana,
                'status'         => $p->status,
                'status_kapal'   => $p->jadwalKapal?->status,
                'cargo_ready_deadline' => $kebutuhan?->deadline,
                'qty_terproduksi'=> (float)($kebutuhan?->qty_terproduksi ?? 0),
                'qty_butuh_produksi' => (float)($kebutuhan?->qty_butuh ?? 0),
            ];
        });

        // Stok CPO per storage
        $stokCpo = StokProduk::with(['storage','produk'])
            ->whereHas('produk', fn($q) => $q->where('kategori','CPO'))
            ->get()
            ->map(fn($s) => [
                'storage'     => $s->storage?->nama,
                'qty'         => (float)$s->qty,
                'harga_satuan'=> (float)($s->harga_satuan ?? 0),
                'mata_uang'   => $s->mata_uang,
                'nilai_total' => (float)$s->qty * (float)($s->harga_satuan ?? 0),
            ]);

        // Stok Produk Jadi
        $stokProduk = StokProduk::with(['storage','produk'])
            ->whereHas('produk', fn($q) => $q->whereNotIn('kategori',['CPO']))
            ->get()
            ->groupBy(fn($s) => $s->produk?->nama_produk)
            ->map(fn($grp) => [
                'produk' => $grp->first()->produk?->nama_produk,
                'total'  => (float)$grp->sum('qty'),
                'details'=> $grp->map(fn($s) => [
                    'storage' => $s->storage?->nama,
                    'qty'     => (float)$s->qty,
                    'harga_satuan' => (float)($s->harga_satuan ?? 0),
                ])->values(),
            ])->values();

        // Proyeksi Pendapatan dari kontrak aktif
        $proyeksi = KontrakPenjualan::where('status','aktif')
            ->with(['buyer','produk'])
            ->get()
            ->map(fn($k) => [
                'nomor_kontrak'    => $k->nomor_kontrak,
                'buyer'            => $k->buyer?->nama,
                'produk'           => $k->produk?->nama_produk,
                'jenis'            => $k->jenis,
                'mata_uang'        => $k->mata_uang,
                'outstanding_qty'  => $k->outstanding_qty,
                'harga_satuan'     => (float)$k->harga_satuan,
                'proyeksi_pendapatan' => $k->proyeksi_pendapatan,
                'kurs_konversi'    => (float)($k->kurs_konversi ?? 1),
            ]);

        // Rekap Kapal Aktif
        $kapals = JadwalKapal::whereNotIn('status',['done'])
            ->with(['pengirimanPenjualans.kontrakPenjualan.buyer'])
            ->orderBy('laycan_start')
            ->get();

        return response()->json([
            'pemenuhan_sales'  => $pengirimanAktif,
            'stok_cpo'         => $stokCpo,
            'stok_produk'      => $stokProduk,
            'proyeksi'         => $proyeksi,
            'total_proyeksi_idr' => $proyeksi->sum(fn($p) =>
                $p['mata_uang'] === 'USD'
                    ? $p['proyeksi_pendapatan'] * $p['kurs_konversi']
                    : $p['proyeksi_pendapatan']
            ),
            'jadwal_kapals'    => $kapals,
        ]);
    }
}
