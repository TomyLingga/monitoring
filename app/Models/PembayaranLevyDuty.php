<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PembayaranLevyDuty extends Model {
    protected $table = 'pembayaran_levy_duties';
    protected $fillable = ['invoice_id','bank_account_id','bank_transaction_id','tgl_bayar','nominal_usd','kurs','nominal_idr','keterangan'];
    protected $casts = ['nominal_usd'=>'decimal:2','kurs'=>'decimal:2','nominal_idr'=>'decimal:2','tgl_bayar'=>'date'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}
