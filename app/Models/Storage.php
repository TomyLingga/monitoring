<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'lokasi', 'kapasitas', 'jenis'];

    public function incomingCpos()
    {
        return $this->hasMany(IncomingCpo::class);
    }

    public function stokProduks()
    {
        return $this->hasMany(StokProduk::class);
    }
}
