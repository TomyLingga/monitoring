<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model {
    protected $fillable = ['nama','kode','tipe','kapasitas','lokasi','keterangan'];
    public function stokProduks() { return $this->hasMany(StokProduk::class); }
    public function incomingCpos() { return $this->hasMany(IncomingCpo::class); }
}
