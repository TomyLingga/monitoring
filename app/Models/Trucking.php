<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trucking extends Model
{
    use HasFactory;
    protected $fillable = ['pengiriman_id', 'no_do', 'qty', 'unit_tersedia', 'transporter', 'tgl'];
    protected $casts = ['qty' => 'decimal:2', 'tgl' => 'date'];

    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
}
