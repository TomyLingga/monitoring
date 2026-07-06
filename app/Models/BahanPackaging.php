<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanPackaging extends Model
{
    use HasFactory;
    protected $fillable = ['proses_packaging_id', 'produk_id', 'qty'];
    protected $casts = ['qty' => 'decimal:2'];

    public function prosesPackaging() { return $this->belongsTo(ProsesPackaging::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
}
