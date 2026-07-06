<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'produk_id', 'nomor_kontrak', 'qty', 'harga_satuan',
        'tgl_kontrak', 'tgl_jatuh_tempo', 'termin_pembayaran', 'status'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'tgl_kontrak' => 'date',
        'tgl_jatuh_tempo' => 'date',
    ];

    protected $appends = ['total_terkirim', 'outstanding_qty', 'total_nilai_kontrak'];

    public function buyer() { return $this->belongsTo(Buyer::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function pengirimanPenjualans() { return $this->hasMany(PengirimanPenjualan::class); }

    public function getTotalTerkirimAttribute()
    {
        return (float) $this->pengirimanPenjualans()->sum('qty_kirim');
    }

    public function getOutstandingQtyAttribute()
    {
        return (float) $this->qty - $this->total_terkirim;
    }

    public function getTotalNilaiKontrakAttribute()
    {
        return (float) $this->qty * (float) $this->harga_satuan;
    }
}
