<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\DailyBankBalance;
use App\Models\Payment;
use App\Models\BankTransaction;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    // --- Bank Accounts ---
    public function getBankAccounts()
    {
        return response()->json(BankAccount::orderBy('bank_name')->get());
    }

    public function storeBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'currency' => 'required|in:IDR,USD',
        ]);
        return response()->json(BankAccount::create($validated), 201);
    }

    public function updateBankAccount(Request $request, $id)
    {
        $bank = BankAccount::findOrFail($id);
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'currency' => 'required|in:IDR,USD',
        ]);
        $bank->update($validated);
        return response()->json($bank);
    }

    public function destroyBankAccount($id)
    {
        BankAccount::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // --- Bank Transactions (Uang Masuk/Keluar) ---
    public function getBankTransactions(Request $request)
    {
        $query = BankTransaction::with('bankAccount');
        
        if ($request->bank_account_id) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }
        return response()->json($query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->get());
    }

    public function storeBankTransaction(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);
        
        $transaction = BankTransaction::create($validated);
        $this->recalculateDailyBalances($transaction->bank_account_id);

        return response()->json($transaction, 201);
    }

    public function updateBankTransaction(Request $request, $id)
    {
        $transaction = BankTransaction::findOrFail($id);
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        $oldBankAccountId = $transaction->bank_account_id;
        $transaction->update($validated);

        if ($oldBankAccountId != $transaction->bank_account_id) {
            $this->recalculateDailyBalances($oldBankAccountId);
        }
        $this->recalculateDailyBalances($transaction->bank_account_id);

        return response()->json($transaction);
    }

    public function destroyBankTransaction($id)
    {
        $transaction = BankTransaction::findOrFail($id);
        $bankAccountId = $transaction->bank_account_id;
        $transaction->delete();
        
        $this->recalculateDailyBalances($bankAccountId);
        return response()->json(null, 204);
    }

    private function recalculateDailyBalances($bankAccountId)
    {
        // Get all transactions for this bank account ordered by date
        $transactions = BankTransaction::where('bank_account_id', $bankAccountId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        // Delete existing balances to recreate them based on transactions
        DailyBankBalance::where('bank_account_id', $bankAccountId)->delete();

        $runningBalance = 0;
        $balancesToInsert = [];
        $lastDate = null;

        foreach ($transactions as $txn) {
            if ($txn->type === 'in') {
                $runningBalance += $txn->amount;
            } else {
                $runningBalance -= $txn->amount;
            }

            // We store the EOD balance for each day there's a transaction
            $balancesToInsert[$txn->transaction_date] = $runningBalance;
        }

        // Insert into DailyBankBalance
        foreach ($balancesToInsert as $date => $balance) {
            DailyBankBalance::create([
                'bank_account_id' => $bankAccountId,
                'tgl' => $date,
                'balance' => $balance
            ]);
        }
    }

    // --- Daily Bank Balances (Read Only mostly, as it's auto-calculated now) ---
    public function getBalances(Request $request)
    {
        $query = DailyBankBalance::with('bankAccount');
        if ($request->bank_account_id) {
            $query->where('bank_account_id', $request->bank_account_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl', [$request->start_date, $request->end_date]);
        }
        return response()->json($query->orderBy('tgl', 'desc')->get());
    }

    // --- Payments (Tagihan Induk) ---
    public function getPayments(Request $request)
    {
        $query = Payment::with(['supplier', 'bankAccount', 'paymentHistories.bankAccount']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->start_date && $request->end_date) {
            // Either created within range, or has histories within range
            $query->where(function($q) use ($request) {
                $q->whereBetween('payment_date', [$request->start_date, $request->end_date])
                  ->orWhereHas('paymentHistories', function($sq) use ($request) {
                      $sq->whereBetween('payment_date', [$request->start_date, $request->end_date]);
                  });
            });
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric',
            'fr_number' => 'nullable|string',
            'job_object' => 'nullable|string',
            'status' => 'required|in:proses,selesai',
        ]);
        return response()->json(Payment::create($validated), 201);
    }

    public function updatePayment(Request $request, $id)
    {
        $pay = Payment::findOrFail($id);
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric',
            'fr_number' => 'nullable|string',
            'job_object' => 'nullable|string',
            'status' => 'required|in:proses,selesai',
        ]);
        $pay->update($validated);
        return response()->json($pay);
    }

    public function destroyPayment($id)
    {
        Payment::findOrFail($id)->delete(); // Cascades to histories
        return response()->json(null, 204);
    }

    // --- Payment Histories (Cicilan/Bayar Paralel) ---
    public function storePaymentHistory(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $history = PaymentHistory::create($validated);
            
            // Auto create bank transaction for uang keluar
            $payment = Payment::with('supplier')->findOrFail($validated['payment_id']);
            $supplierName = $payment->supplier ? $payment->supplier->nama : 'Unknown';
            
            $txn = BankTransaction::create([
                'bank_account_id' => $history->bank_account_id,
                'type' => 'out',
                'amount' => $history->amount,
                'transaction_date' => $history->payment_date,
                'description' => 'Pembayaran tagihan ke ' . $supplierName . ($payment->fr_number ? ' (FR: '.$payment->fr_number.')' : ''),
                'reference_type' => 'PaymentHistory',
                'reference_id' => $history->id
            ]);

            $this->recalculateDailyBalances($history->bank_account_id);
            
            // Auto update status if paid amount >= total amount
            $totalPaid = PaymentHistory::where('payment_id', $payment->id)->sum('amount');
            if ($totalPaid >= $payment->amount) {
                $payment->update(['status' => 'selesai']);
            }

            DB::commit();
            return response()->json($history, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyPaymentHistory($id)
    {
        DB::beginTransaction();
        try {
            $history = PaymentHistory::findOrFail($id);
            $bankAccountId = $history->bank_account_id;
            
            // Delete associated bank transaction
            BankTransaction::where('reference_type', 'PaymentHistory')
                ->where('reference_id', $history->id)
                ->delete();
                
            $history->delete();
            $this->recalculateDailyBalances($bankAccountId);
            
            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // --- BI Kurs API ---
    public function getKurs(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::today()->subMonths(5)->toDateString());
        $end_date = $request->input('end_date', Carbon::today()->toDateString());
        
        $urlBase = env('URL_KURS', 'https://www.bi.go.id/biwebservice/wskursbi.asmx/getSubKursAsing3?mts=');
        $currency = 'USD'; 
        $url = $urlBase . $currency . "&startdate={$start_date}&enddate={$end_date}";
        
        try {
            $xmlString = @file_get_contents($url);
            if (!$xmlString) {
                return response()->json([]);
            }
            
            $xml = simplexml_load_string($xmlString);
            
            $data = [];
            if ($xml && isset($xml->children('diffgr', true)->diffgram)) {
                $dataset = $xml->children('diffgr', true)->diffgram->children()->NewDataSet->children();
                foreach ($dataset as $table) {
                    $beli = (float) $table->beli_subkursasing;
                    $jual = (float) $table->jual_subkursasing;
                    $tgl = date('Y-m-d', strtotime((string) $table->tgl_subkursasing));
                    
                    $data[] = [
                        'tanggal' => $tgl,
                        'value' => ($beli + $jual) / 2, 
                    ];
                }
            }
            
            usort($data, function ($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
