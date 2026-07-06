<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTarget extends Model
{
    use HasFactory;

    protected $table = 'daily_targets';

    protected $fillable = [
        'tgl',
        'target_qty',
    ];
}
