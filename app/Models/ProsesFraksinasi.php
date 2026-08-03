<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProsesFraksinasi extends Model {
    protected $fillable = ['storage_id','tgl_proses','total_bahan','total_hasil','losses','keterangan'];
    protected $casts = ['tgl_proses'=>'date','total_bahan'=>'decimal:2','total_hasil'=>'decimal:2','losses'=>'decimal:2'];
    public function storage() { return $this->belongsTo(Storage::class); }
    public function bahans() { return $this->hasMany(BahanFraksinasi::class, 'proses_fraksinasi_id'); }
    public function hasils() { return $this->hasMany(HasilFraksinasi::class, 'proses_fraksinasi_id'); }
}
class BahanFraksinasi extends Model {
    protected $table = 'bahan_fraksinasis';
    protected $fillable = ['proses_fraksinasi_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
class HasilFraksinasi extends Model {
    protected $table = 'hasil_fraksinasis';
    protected $fillable = ['proses_fraksinasi_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
