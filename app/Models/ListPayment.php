<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPayment extends Model
{
    use HasFactory;
    protected $fillable = ['mitra', 'jumlah', 'nomor_fr', 'bank', 'objek_pekerjaan', 'tgl_pembayaran_realisasi'];
    protected $casts = ['jumlah' => 'decimal:2', 'tgl_pembayaran_realisasi' => 'date'];
}
