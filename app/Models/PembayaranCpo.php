<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranCpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kontrak_cpo_id', 'nominal', 'tgl_bayar',
        'metode_bayar', 'bukti_bayar', 'catatan', 'bank_account_id'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tgl_bayar' => 'date',
    ];

    public function kontrakCpo()
    {
        return $this->belongsTo(KontrakCpo::class);
    }
}
