<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'alamat', 'telepon', 'email', 'pic', 'keterangan'];

    public function kontrakCpos()
    {
        return $this->hasMany(KontrakCpo::class);
    }
}
