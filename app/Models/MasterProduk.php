<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduk extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode'];

    public function stokProduks()
    {
        return $this->hasMany(StokProduk::class, 'produk_id');
    }

    public function kontrakPenjualans()
    {
        return $this->hasMany(KontrakPenjualan::class, 'produk_id');
    }
}
