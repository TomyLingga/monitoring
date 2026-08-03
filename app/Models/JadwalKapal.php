<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JadwalKapal extends Model {
    protected $fillable = [
        'nama_kapal','nomor_voyage','bendera','laycan_start','laycan_end',
        'eta','etb','etd','port_muat','port_bongkar','status','keterangan'
    ];
    protected $casts = ['laycan_start'=>'date','laycan_end'=>'date','eta'=>'date','etb'=>'date','etd'=>'date'];

    public function pengirimanPenjualans() { return $this->hasMany(PengirimanPenjualan::class, 'jadwal_kapal_id'); }

    public function getTotalQtyAttribute() {
        return (float)$this->pengirimanPenjualans()->sum('qty_order');
    }
}
