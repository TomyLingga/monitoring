<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProsesRefinery extends Model {
    protected $fillable = ['storage_id','tgl_proses','total_bahan','total_hasil','losses','keterangan'];
    protected $casts = ['tgl_proses'=>'date','total_bahan'=>'decimal:2','total_hasil'=>'decimal:2','losses'=>'decimal:2'];
    public function storage() { return $this->belongsTo(Storage::class); }
    public function bahans() { return $this->hasMany(BahanRefinery::class, 'proses_refinery_id'); }
    public function hasils() { return $this->hasMany(HasilRefinery::class, 'proses_refinery_id'); }
}
class BahanRefinery extends Model {
    protected $table = 'bahan_refineries';
    protected $fillable = ['proses_refinery_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
class HasilRefinery extends Model {
    protected $table = 'hasil_refineries';
    protected $fillable = ['proses_refinery_id','produk_id','storage_id','qty'];
    protected $casts = ['qty'=>'decimal:2'];
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
}
