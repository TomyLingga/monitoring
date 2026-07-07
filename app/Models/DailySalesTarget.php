<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesTarget extends Model
{
    use HasFactory;
    
    protected $fillable = ['tgl', 'target_qty'];
}
