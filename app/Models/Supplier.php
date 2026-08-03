<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = ['nama','kode','alamat','kontak','keterangan'];
    public function kontrakCpos() { return $this->hasMany(KontrakCpo::class); }
}
