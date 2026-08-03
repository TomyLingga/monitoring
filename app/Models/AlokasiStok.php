<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AlokasiStok extends Model {
    protected $fillable = ['pengiriman_id','storage_id','produk_id','qty_alokasi','tgl_alokasi','keterangan'];
    protected $casts = ['qty_alokasi'=>'decimal:2','tgl_alokasi'=>'date'];
    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
    public function storage() { return $this->belongsTo(Storage::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
}
