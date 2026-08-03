<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model {
    protected $fillable = ['nama','kode','negara','kontak','keterangan'];
    public function kontrakPenjualans() { return $this->hasMany(KontrakPenjualan::class); }
}
