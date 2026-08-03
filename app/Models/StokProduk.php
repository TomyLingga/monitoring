<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model {
    protected $fillable = ['storage_id','produk_id','qty','harga_satuan','mata_uang','tgl_update','keterangan'];
    protected $casts = ['qty'=>'decimal:2','harga_satuan'=>'decimal:2','tgl_update'=>'date'];
    protected $appends = ['nilai_total'];

    public function storage() { return $this->belongsTo(Storage::class); }
    public function produk() { return $this->belongsTo(MasterProduk::class, 'produk_id'); }

    public function getNilaiTotalAttribute() {
        return (float)$this->qty * (float)($this->harga_satuan ?? 0);
    }
}
