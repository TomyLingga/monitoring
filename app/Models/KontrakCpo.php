<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakCpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'nomor_kontrak', 'jenis', 'mata_uang', 'qty', 'harga_per_kg',
        'cbd_cad', 'tgl_kontrak', 'tgl_jatuh_tempo', 'status', 'is_closed'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
        'tgl_kontrak' => 'date',
        'tgl_jatuh_tempo' => 'date',
        'is_closed' => 'boolean',
    ];

    protected $appends = [
        'total_terkirim', 'outstanding_qty',
        'total_nilai_kontrak', 'total_terbayar', 'outstanding_nominal'
    ];

    // ── Relationships ──

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function incomingCpos()
    {
        return $this->hasMany(IncomingCpo::class);
    }

    public function pembayaranCpos()
    {
        return $this->hasMany(PembayaranCpo::class);
    }

    // ── Accessors: Outstanding Tracking ──

    /** Total qty CPO yang sudah dikirim/diterima */
    public function getTotalTerkirimAttribute()
    {
        return (float) $this->incomingCpos()->sum('qty_terima');
    }

    /** Outstanding qty = kontrak qty - total terkirim */
    public function getOutstandingQtyAttribute()
    {
        return (float) $this->qty - $this->total_terkirim;
    }

    /** Total nilai kontrak = qty × harga_per_kg */
    public function getTotalNilaiKontrakAttribute()
    {
        return (float) $this->qty * (float) $this->harga_per_kg;
    }

    /** Total nominal yang sudah dibayar */
    public function getTotalTerbayarAttribute()
    {
        return (float) $this->pembayaranCpos()->sum('nominal');
    }

    /** Outstanding nominal = total nilai kontrak - total terbayar */
    public function getOutstandingNominalAttribute()
    {
        return $this->total_nilai_kontrak - $this->total_terbayar;
    }
}
