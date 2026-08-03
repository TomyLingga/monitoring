<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KebutuhanPe extends Model {
    protected $table = 'kebutuhan_pes';
    protected $fillable = ['tgl','periode','qty_kebutuhan','qty_realisasi','keterangan'];
    protected $casts = ['qty_kebutuhan'=>'decimal:2','qty_realisasi'=>'decimal:2','tgl'=>'date'];
    protected $appends = ['outstanding'];

    public function penguranganPes() { return $this->hasMany(PenguranganPe::class, 'kebutuhan_pe_id'); }

    public function getOutstandingAttribute() {
        $dikurangi = (float)$this->penguranganPes()->sum('qty');
        return max(0, (float)$this->qty_kebutuhan - $dikurangi);
    }
}
