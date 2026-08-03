<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KontrakPenjualan extends Model {
    protected $fillable = [
        'buyer_id','produk_id','nomor_kontrak','jenis','mata_uang',
        'qty','harga_satuan','kurs_konversi','incoterm','levy_rate_usd',
        'termin_pembayaran','metode_invoice','tgl_kontrak','tgl_jatuh_tempo','status','keterangan'
    ];
    protected $casts = [
        'qty'=>'decimal:2','harga_satuan'=>'decimal:2','kurs_konversi'=>'decimal:2',
        'levy_rate_usd'=>'decimal:4','tgl_kontrak'=>'date','tgl_jatuh_tempo'=>'date',
    ];
    protected $appends = ['total_terkirim','outstanding_qty','total_nilai','total_terbayar','outstanding_nominal','proyeksi_pendapatan'];

    public function buyer() { return $this->belongsTo(Buyer::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function pengirimanPenjualans() { return $this->hasMany(PengirimanPenjualan::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function pembayaranPenjualans() { return $this->hasMany(PembayaranPenjualan::class); }

    public function getTotalTerkirimAttribute() {
        return (float)$this->pengirimanPenjualans()->whereNotNull('qty_kirim')->sum('qty_kirim');
    }
    public function getOutstandingQtyAttribute() {
        return (float)$this->qty - $this->total_terkirim;
    }
    public function getTotalNilaiAttribute() {
        return (float)$this->qty * (float)$this->harga_satuan;
    }
    public function getTotalTerbayarAttribute() {
        return (float)$this->pembayaranPenjualans()->sum('nominal');
    }
    public function getOutstandingNominalAttribute() {
        return $this->total_nilai - $this->total_terbayar;
    }
    public function getProyeksiPendapatanAttribute() {
        return $this->outstanding_qty * (float)$this->harga_satuan;
    }
}
