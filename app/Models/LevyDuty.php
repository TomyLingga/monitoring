<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevyDuty extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'kapal', 'tarif', 'kurs', 'nilai_akhir', 'bank_account_id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
