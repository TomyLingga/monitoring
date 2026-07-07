<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticAdditionalPe extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'tgl',
        'keterangan',
    ];
    protected $casts = ['qty' => 'decimal:2', 'tgl' => 'date'];
}
