<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PembayaranCpo extends Model {
    protected $fillable = ['kontrak_cpo_id','bank_account_id','bank_transaction_id','tgl_bayar','nominal','mata_uang','kurs','keterangan'];
    protected $casts = ['nominal'=>'decimal:2','kurs'=>'decimal:2','tgl_bayar'=>'date'];
    public function kontrakCpo() { return $this->belongsTo(KontrakCpo::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}
