<?php

namespace App\Http\Controllers;

use App\Models\LevyDuty;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LevyDutyController extends Controller
{
    public function index()
    {
        return response()->json(LevyDuty::with('invoice')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'kapal' => 'nullable|string',
            'tarif' => 'numeric',
            'kurs' => 'numeric',
            'nilai_akhir' => 'numeric',
        ]);
        
        return DB::transaction(function () use ($validated) {
            $levy = LevyDuty::create($validated);

            BankTransaction::create([
                'bank_account_id' => $validated['bank_account_id'],
                'type' => 'out',
                'amount' => $validated['nilai_akhir'],
                'transaction_date' => now(), // Assume paid today
                'description' => 'Pembayaran Levy Duty Invoice ID ' . $validated['invoice_id'] . ($validated['kapal'] ? ' Kapal: ' . $validated['kapal'] : ''),
                'reference_type' => LevyDuty::class,
                'reference_id' => $levy->id,
            ]);

            return response()->json($levy->load('invoice'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $levy = LevyDuty::findOrFail($id);
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'kapal' => 'nullable|string',
            'tarif' => 'numeric',
            'kurs' => 'numeric',
            'nilai_akhir' => 'numeric',
        ]);
        
        return DB::transaction(function () use ($validated, $levy) {
            $levy->update($validated);

            $txn = BankTransaction::where('reference_type', LevyDuty::class)->where('reference_id', $levy->id)->first();
            if ($txn) {
                $txn->update([
                    'bank_account_id' => $validated['bank_account_id'],
                    'amount' => $validated['nilai_akhir'],
                    'description' => 'Pembayaran Levy Duty Invoice ID ' . $validated['invoice_id'] . ($validated['kapal'] ? ' Kapal: ' . $validated['kapal'] : ''),
                ]);
            } else {
                BankTransaction::create([
                    'bank_account_id' => $validated['bank_account_id'],
                    'type' => 'out',
                    'amount' => $validated['nilai_akhir'],
                    'transaction_date' => now(),
                    'description' => 'Pembayaran Levy Duty Invoice ID ' . $validated['invoice_id'] . ($validated['kapal'] ? ' Kapal: ' . $validated['kapal'] : ''),
                    'reference_type' => LevyDuty::class,
                    'reference_id' => $levy->id,
                ]);
            }

            return response()->json($levy->load('invoice'));
        });
    }

    public function destroy($id)
    {
        $levy = LevyDuty::findOrFail($id);
        
        return DB::transaction(function () use ($levy) {
            BankTransaction::where('reference_type', LevyDuty::class)->where('reference_id', $levy->id)->delete();
            $levy->delete();
            return response()->json(null, 204);
        });
    }
}
