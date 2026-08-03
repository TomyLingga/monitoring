<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Trucking extends Model {
    protected $fillable = [
        'pengiriman_id','tgl','transporter_name','destination','qty_unit',
        'qty_produk','produk_id','no_polisi','no_surat_jalan','keterangan'
    ];
    protected $casts = ['qty_produk'=>'decimal:2','tgl'=>'date'];
    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }
}
