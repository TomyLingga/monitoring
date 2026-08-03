<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterProduk extends Model {
    protected $table = 'master_produks';
    protected $fillable = ['nama_produk','kode_produk','kategori','satuan','yield_dari_cpo','keterangan'];
    public function stokProduks() { return $this->hasMany(StokProduk::class, 'produk_id'); }
    public function kontrakPenjualans() { return $this->hasMany(KontrakPenjualan::class, 'produk_id'); }
}
