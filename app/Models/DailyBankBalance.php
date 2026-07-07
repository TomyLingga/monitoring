<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyBankBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'balance',
        'tgl',
        'exchange_rate'
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
