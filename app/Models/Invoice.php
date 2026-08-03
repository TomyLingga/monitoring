<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    protected $fillable = [
        'kontrak_penjualan_id','pengiriman_id','nomor_invoice','qty','harga_satuan',
        'mata_uang','kurs_konversi','nilai_invoice','levy_amount','levy_kurs',
        'tgl_invoice','tgl_jatuh_tempo','status','keterangan'
    ];
    protected $casts = [
        'qty'=>'decimal:2','harga_satuan'=>'decimal:2','kurs_konversi'=>'decimal:2',
        'nilai_invoice'=>'decimal:2','levy_amount'=>'decimal:2','levy_kurs'=>'decimal:2',
        'tgl_invoice'=>'date','tgl_jatuh_tempo'=>'date',
    ];
    protected $appends = ['total_nilai_idr'];

    public function kontrakPenjualan() { return $this->belongsTo(KontrakPenjualan::class); }
    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
    public function pembayaranPenjualans() { return $this->hasMany(PembayaranPenjualan::class); }
    public function pembayaranLevyDuties() { return $this->hasMany(PembayaranLevyDuty::class); }

    public function getTotalNilaiIdrAttribute() {
        if ($this->mata_uang === 'USD' && $this->kurs_konversi) {
            return (float)$this->nilai_invoice * (float)$this->kurs_konversi;
        }
        return (float)$this->nilai_invoice;
    }
}
