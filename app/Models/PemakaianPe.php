<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianPe extends Model
{
    use HasFactory;
    protected $fillable = ['qty', 'tgl'];
    protected $casts = ['qty' => 'decimal:2', 'tgl' => 'date'];
}
