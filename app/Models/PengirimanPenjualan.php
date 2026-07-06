<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanPenjualan extends Model
{
    use HasFactory;
    protected $fillable = [
        'kontrak_penjualan_id', 'qty_kirim', 'qty_terima',
        'via', 'termin', 'status', 'incoterm', 'tgl'
    ];
    protected $casts = ['qty_kirim' => 'decimal:2', 'qty_terima' => 'decimal:2', 'tgl' => 'date'];

    public function kontrakPenjualan() { return $this->belongsTo(KontrakPenjualan::class); }
    public function invoices() { return $this->hasMany(Invoice::class, 'pengiriman_id'); }
    public function truckings() { return $this->hasMany(Trucking::class, 'pengiriman_id'); }
}
