<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KebutuhanProduksi extends Model {
    protected $fillable = ['pengiriman_id','produk_id','qty_butuh','qty_terproduksi','deadline','status','keterangan'];
    protected $casts = ['qty_butuh'=>'decimal:2','qty_terproduksi'=>'decimal:2','deadline'=>'date'];
    protected $appends = ['outstanding'];

    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }

    public function getOutstandingAttribute() {
        return max(0, (float)$this->qty_butuh - (float)$this->qty_terproduksi);
    }
}
