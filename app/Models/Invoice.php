<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['pengiriman_id', 'nomor_invoice', 'nilai', 'tgl_invoice', 'tgl_jatuh_tempo', 'status'];
    protected $casts = ['nilai' => 'decimal:2', 'tgl_invoice' => 'date', 'tgl_jatuh_tempo' => 'date'];

    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
    public function pembayaranLevyDuties() { return $this->hasMany(PembayaranLevyDuty::class); }
}
