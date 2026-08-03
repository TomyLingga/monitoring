<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProsesPackaging extends Model {
    protected $fillable = ['storage_id','tgl_proses','total_bahan','total_hasil','losses','keterangan'];
    protected $casts = ['tgl_proses'=>'date','total_bahan'=>'decimal:2','total_hasil'=>'decimal:2','losses'=>'decimal:2'];
    public function storage() { return $this->belongsTo(Storage::class); }
    public function bahans() { return $this->hasMany(BahanPackaging::class, 'proses_packaging_id'); }
    public function hasils() { return $this->hasMany(HasilPackaging::class, 'proses_packaging_id'); }
}
class BahanPackaging extends Model {
    protected $table = 'bahan_packagings';
    protected $fillable = ['proses_packaging_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
class HasilPackaging extends Model {
    protected $table = 'hasil_packagings';
    protected $fillable = ['proses_packaging_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
