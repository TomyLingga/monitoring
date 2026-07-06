<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesPackaging extends Model
{
    use HasFactory;
    protected $fillable = ['tgl', 'shift', 'operator', 'catatan'];
    protected $casts = ['tgl' => 'date'];

    public function bahanPackagings() { return $this->hasMany(BahanPackaging::class); }
    public function hasilPackagings() { return $this->hasMany(HasilPackaging::class); }
}
