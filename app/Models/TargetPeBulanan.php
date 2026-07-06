<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetPeBulanan extends Model
{
    use HasFactory;
    protected $fillable = ['jumlah', 'tgl'];
    protected $casts = ['jumlah' => 'decimal:2', 'tgl' => 'date'];
}
