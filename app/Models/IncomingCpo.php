<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IncomingCpo extends Model {
    protected $fillable = [
        'kontrak_cpo_id','kebutuhan_cpo_do_id','storage_id','nomor_do','qty_order','qty_terima',
        'harga_per_kg','tgl_rencana','tgl_terima','no_dok','keterangan'
    ];
    protected $casts = ['qty_order'=>'decimal:2','qty_terima'=>'decimal:2','harga_per_kg'=>'decimal:2','tgl_terima'=>'date','tgl_rencana'=>'date'];

    public function kontrakCpo() { return $this->belongsTo(KontrakCpo::class); }
    public function storage() { return $this->belongsTo(Storage::class); }
    public function kebutuhanCpoDo() { return $this->belongsTo(KebutuhanCpoDo::class); }
}
