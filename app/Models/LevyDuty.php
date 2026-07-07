<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevyDuty extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'kapal', 'tarif', 'kurs', 'nilai_akhir'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
