<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilRefinery extends Model
{
    use HasFactory;
    protected $fillable = ['proses_refinery_id', 'produk_id', 'qty', 'storage_id'];
    protected $casts = ['qty' => 'decimal:2'];

    public function prosesRefinery() { return $this->belongsTo(ProsesRefinery::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
    public function storage() { return $this->belongsTo(Storage::class, 'storage_id'); }
}
