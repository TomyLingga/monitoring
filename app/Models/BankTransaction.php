<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model {
    protected $fillable = [
        'bank_account_id','tgl_transaksi','tipe','nominal','mata_uang',
        'kurs','kategori','referensi','transactable_type','transactable_id','keterangan'
    ];
    protected $casts = ['nominal'=>'decimal:2','kurs'=>'decimal:2','tgl_transaksi'=>'date'];
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function transactable() { return $this->morphTo(); }
}
