<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PengirimanPenjualan extends Model {
    protected $fillable = [
        'kontrak_penjualan_id','jadwal_kapal_id','nomor_do','qty_order',
        'qty_alokasi','qty_kirim','qty_terima',
        'kebutuhan_cpo_kg','kebutuhan_cpo_terpenuhi',
        'tgl_rencana','tgl_realisasi',
        'laycan_start','laycan_end','via','status','termin','keterangan'
    ];
    protected $casts = [
        'qty_order'=>'decimal:2','qty_alokasi'=>'decimal:2','qty_kirim'=>'decimal:2','qty_terima'=>'decimal:2',
        'kebutuhan_cpo_kg'=>'decimal:2','kebutuhan_cpo_terpenuhi'=>'decimal:2',
        'tgl_rencana'=>'date','tgl_realisasi'=>'date','laycan_start'=>'date','laycan_end'=>'date',
    ];

    public function kontrakPenjualan() { return $this->belongsTo(KontrakPenjualan::class); }
    public function jadwalKapal() { return $this->belongsTo(JadwalKapal::class); }
    public function alokasis() { return $this->hasMany(AlokasiStok::class, 'pengiriman_id'); }
    public function truckings() { return $this->hasMany(Trucking::class, 'pengiriman_id'); }
    public function kebutuhanProduksis() { return $this->hasMany(KebutuhanProduksi::class, 'pengiriman_id'); }
    public function invoices() { return $this->hasMany(Invoice::class, 'pengiriman_id'); }
    public function kebutuhanCpoDos() { return $this->hasMany(KebutuhanCpoDo::class, 'pengiriman_id'); }

    // Auto-sum qty_alokasi from alokasi_stoks
    public function recalculateAlokasi() {
        $this->qty_alokasi = (float)$this->alokasis()->sum('qty_alokasi');
        $this->save();
    }

    // Kekurangan stok = order - alokasi
    public function getKekuranganStokAttribute() {
        return max(0, (float)$this->qty_order - (float)($this->qty_alokasi ?? 0));
    }

    // Kekurangan CPO = kekurangan stok / yield
    public function getKekuranganCpoAttribute() {
        $kekurangan = $this->kekurangan_stok;
        $yield = $this->kontrakPenjualan?->produk?->yield_dari_cpo ?? 0.82;
        return $yield > 0 ? $kekurangan / $yield : 0;
    }
}
