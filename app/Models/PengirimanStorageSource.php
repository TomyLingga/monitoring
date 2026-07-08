<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanStorageSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengiriman_penjualan_id',
        'storage_id',
        'qty'
    ];

    public function pengirimanPenjualan()
    {
        return $this->belongsTo(PengirimanPenjualan::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }
}
