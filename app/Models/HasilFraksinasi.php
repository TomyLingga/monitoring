<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilFraksinasi extends Model
{
    use HasFactory;
    protected $fillable = ['proses_fraksinasi_id', 'produk_id', 'qty'];
    protected $casts = ['qty' => 'decimal:2'];

    public function prosesFraksinasi() { return $this->belongsTo(ProsesFraksinasi::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
}
