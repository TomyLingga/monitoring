<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingCpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kontrak_cpo_id', 'storage_id', 'qty_kirim', 'qty_terima', 'selisih_qty', 'tgl', 'note'
    ];

    protected $casts = [
        'qty_kirim' => 'decimal:2',
        'qty_terima' => 'decimal:2',
        'selisih_qty' => 'decimal:2',
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
