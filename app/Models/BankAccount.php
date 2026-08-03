<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model {
    protected $fillable = ['nama_bank','nama_rekening','nomor_rekening','mata_uang','saldo_awal','keterangan'];
    protected $casts = ['saldo_awal'=>'decimal:2'];
    protected $appends = ['saldo_saat_ini'];

    public function transactions() { return $this->hasMany(BankTransaction::class); }

    public function getSaldoSaatIniAttribute() {
        $kredit = (float)$this->transactions()->where('tipe','kredit')->sum('nominal');
        $debit  = (float)$this->transactions()->where('tipe','debit')->sum('nominal');
        return (float)$this->saldo_awal + $kredit - $debit;
    }
}
