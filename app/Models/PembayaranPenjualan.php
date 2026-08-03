<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model {
    protected $fillable = ['kontrak_penjualan_id','invoice_id','bank_account_id','bank_transaction_id','tgl_bayar','nominal','mata_uang','kurs','keterangan'];
    protected $casts = ['nominal'=>'decimal:2','kurs'=>'decimal:2','tgl_bayar'=>'date'];
    public function kontrakPenjualan() { return $this->belongsTo(KontrakPenjualan::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}
