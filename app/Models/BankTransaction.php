<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'reference_type',
        'reference_id'
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
