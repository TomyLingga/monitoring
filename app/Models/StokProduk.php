<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model
{
    use HasFactory;

    protected $fillable = ['produk_id', 'storage_id', 'qty'];

    protected $casts = ['qty' => 'decimal:2'];

    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'produk_id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }
}
