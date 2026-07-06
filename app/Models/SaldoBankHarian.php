<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoBankHarian extends Model
{
    use HasFactory;
    protected $fillable = ['saldo', 'tgl', 'no_rek', 'nama_bank', 'kurs'];
    protected $casts = ['saldo' => 'decimal:2', 'kurs' => 'decimal:2', 'tgl' => 'date'];
}
