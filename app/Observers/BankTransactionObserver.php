<?php

namespace App\Observers;

use App\Models\BankTransaction;
use App\Models\DailyBankBalance;

class BankTransactionObserver
{
    public function created(BankTransaction $bankTransaction)
    {
        $this->recalculateDailyBalances($bankTransaction->bank_account_id);
    }

    public function updated(BankTransaction $bankTransaction)
    {
        if ($bankTransaction->isDirty('bank_account_id')) {
            $this->recalculateDailyBalances($bankTransaction->getOriginal('bank_account_id'));
        }
        $this->recalculateDailyBalances($bankTransaction->bank_account_id);
    }

    public function deleted(BankTransaction $bankTransaction)
    {
        $this->recalculateDailyBalances($bankTransaction->bank_account_id);
    }

    private function recalculateDailyBalances($bankAccountId)
    {
        $transactions = BankTransaction::where('bank_account_id', $bankAccountId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        DailyBankBalance::where('bank_account_id', $bankAccountId)->delete();

        $runningBalance = 0;
        $balancesToInsert = [];

        foreach ($transactions as $txn) {
            if ($txn->type === 'in') {
                $runningBalance += $txn->amount;
            } else {
                $runningBalance -= $txn->amount;
            }
            $balancesToInsert[$txn->transaction_date] = $runningBalance;
        }

        foreach ($balancesToInsert as $date => $balance) {
            DailyBankBalance::create([
                'bank_account_id' => $bankAccountId,
                'tgl' => $date,
                'balance' => $balance
            ]);
        }
    }
}
