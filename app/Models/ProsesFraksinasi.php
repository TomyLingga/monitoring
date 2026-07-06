<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesFraksinasi extends Model
{
    use HasFactory;
    protected $fillable = ['tgl', 'shift', 'operator', 'catatan'];
    protected $casts = ['tgl' => 'date'];

    public function bahanFraksinasis() { return $this->hasMany(BahanFraksinasi::class); }
    public function hasilFraksinasis() { return $this->hasMany(HasilFraksinasi::class); }
}
