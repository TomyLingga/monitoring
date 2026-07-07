<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProductionTarget extends Model
{
    use HasFactory;

    protected $fillable = ['tgl', 'jenis', 'target_qty', 'satuan', 'catatan'];
    protected $casts = ['tgl' => 'date', 'target_qty' => 'decimal:2'];
}
