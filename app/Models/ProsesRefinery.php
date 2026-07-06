<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesRefinery extends Model
{
    use HasFactory;

    protected $fillable = ['tgl', 'shift', 'operator', 'catatan'];
    protected $casts = ['tgl' => 'date'];

    public function bahanRefineries()
    {
        return $this->hasMany(BahanRefinery::class);
    }

    public function hasilRefineries()
    {
        return $this->hasMany(HasilRefinery::class);
    }
}
