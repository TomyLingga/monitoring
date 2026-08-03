<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PenguranganPe extends Model {
    protected $table = 'pengurangan_pes';
    protected $fillable = ['kebutuhan_pe_id','tgl','qty','keterangan'];
    protected $casts = ['qty'=>'decimal:2','tgl'=>'date'];
    public function kebutuhanPe() { return $this->belongsTo(KebutuhanPe::class, 'kebutuhan_pe_id'); }
}
