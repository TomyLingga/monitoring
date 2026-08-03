<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KebutuhanCpoDo extends Model {
    protected $fillable = [
        'pengiriman_id','qty_butuh_kg','qty_terpenuhi_kg','deadline','status','catatan'
    ];
    protected $casts = [
        'qty_butuh_kg'=>'decimal:2','qty_terpenuhi_kg'=>'decimal:2',
        'deadline'=>'date',
    ];

    public function pengiriman() { return $this->belongsTo(PengirimanPenjualan::class, 'pengiriman_id'); }

    public function getOutstandingKgAttribute() {
        return max(0, (float)$this->qty_butuh_kg - (float)$this->qty_terpenuhi_kg);
    }

    public function updateStatus() {
        if ($this->qty_terpenuhi_kg >= $this->qty_butuh_kg) {
            $this->status = 'done';
        } elseif ($this->qty_terpenuhi_kg > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }
}
