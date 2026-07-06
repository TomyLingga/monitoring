<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranLevyDuty extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'kapal', 'tarif', 'kurs', 'nilai_akhir'];
    protected $casts = ['tarif' => 'decimal:2', 'kurs' => 'decimal:2', 'nilai_akhir' => 'decimal:2'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}
