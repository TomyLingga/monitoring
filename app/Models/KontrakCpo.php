<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KontrakCpo extends Model {
    protected $fillable = [
        'supplier_id','nomor_kontrak','jenis','mata_uang','qty',
        'harga_per_kg','termin_pembayaran','tgl_kontrak','tgl_jatuh_tempo','status','keterangan'
    ];
    protected $casts = [
        'qty' => 'decimal:2', 'harga_per_kg' => 'decimal:2',
        'tgl_kontrak' => 'date', 'tgl_jatuh_tempo' => 'date',
    ];
    protected $appends = ['total_terkirim','outstanding_qty','total_nilai_kontrak','total_terbayar','outstanding_nominal'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function incomingCpos() { return $this->hasMany(IncomingCpo::class); }
    public function pembayaranCpos() { return $this->hasMany(PembayaranCpo::class); }

    public function getTotalTerkirimAttribute() { return (float)$this->incomingCpos()->sum('qty_terima'); }
    public function getOutstandingQtyAttribute() { return (float)$this->qty - $this->total_terkirim; }
    public function getTotalNilaiKontrakAttribute() { return (float)$this->qty * (float)$this->harga_per_kg; }
    public function getTotalTerbayarAttribute() { return (float)$this->pembayaranCpos()->sum('nominal'); }
    public function getOutstandingNominalAttribute() { return $this->total_nilai_kontrak - $this->total_terbayar; }
}
