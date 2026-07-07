<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_penjualans';

    protected $fillable = [
        'kontrak_penjualan_id', 'nominal', 'tgl_bayar', 'catatan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tgl_bayar' => 'date',
    ];

    public function kontrakPenjualan()
    {
        return $this->belongsTo(KontrakPenjualan::class, 'kontrak_penjualan_id');
    }
}
