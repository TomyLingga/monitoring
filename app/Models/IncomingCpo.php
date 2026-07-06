<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingCpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kontrak_cpo_id', 'storage_id', 'qty', 'tgl',
        'no_surat_jalan', 'supir', 'no_kendaraan', 'note'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'tgl' => 'date',
    ];

    public function kontrakCpo()
    {
        return $this->belongsTo(KontrakCpo::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }
}
