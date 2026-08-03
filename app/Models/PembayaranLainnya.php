<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PembayaranLainnya extends Model {
    protected $table = 'pembayaran_lainnya';
    protected $fillable = ['bank_account_id','bank_transaction_id','tgl_bayar','tipe','nominal','mata_uang','kurs','kategori','keterangan'];
    protected $casts = ['nominal'=>'decimal:2','kurs'=>'decimal:2','tgl_bayar'=>'date'];
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}
