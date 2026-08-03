<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{BankAccount, BankTransaction, PembayaranCpo, PembayaranPenjualan, PembayaranLevyDuty, PembayaranLainnya};
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    // ─── Bank Accounts ────────────────────────────────────────────────────────

    public function getBankAccounts()
    {
        return BankAccount::get()->map(fn($a) => array_merge($a->toArray(), [
            'saldo_saat_ini' => $a->saldo_saat_ini,
        ]));
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate([
            'nama_bank'       => 'required|string',
            'nama_rekening'   => 'required|string',
            'nomor_rekening'  => 'required|string|unique:bank_accounts',
            'mata_uang'       => 'nullable|in:IDR,USD',
            'saldo_awal'      => 'nullable|numeric',
            'keterangan'      => 'nullable|string',
        ]);
        return BankAccount::create($data);
    }

    public function updateBankAccount(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);
        $data = $request->validate([
            'nama_bank'      => 'required|string',
            'nama_rekening'  => 'required|string',
            'nomor_rekening' => 'required|string|unique:bank_accounts,nomor_rekening,'.$id,
            'mata_uang'      => 'nullable|in:IDR,USD',
            'saldo_awal'     => 'nullable|numeric',
            'keterangan'     => 'nullable|string',
        ]);
        $account->update($data);
        return $account;
    }

    public function destroyBankAccount($id)
    {
        BankAccount::findOrFail($id)->delete();
        return response()->json(['message' => 'Rekening dihapus']);
    }

    // ─── Bank Transactions ────────────────────────────────────────────────────

    public function getBankTransactions(Request $request)
    {
        $q = BankTransaction::with('bankAccount');
        if ($request->bank_account_id) $q->where('bank_account_id',$request->bank_account_id);
        if ($request->tgl_start)       $q->whereDate('tgl_transaksi','>=',$request->tgl_start);
        if ($request->tgl_end)         $q->whereDate('tgl_transaksi','<=',$request->tgl_end);
        return $q->orderBy('tgl_transaksi','desc')->get();
    }

    public function storeBankTransaction(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'tgl_transaksi'   => 'required|date',
            'tipe'            => 'required|in:debit,kredit',
            'nominal'         => 'required|numeric|min:0',
            'mata_uang'       => 'nullable|in:IDR,USD',
            'kurs'            => 'nullable|numeric',
            'kategori'        => 'nullable|string',
            'referensi'       => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);
        return BankTransaction::create($data)->load('bankAccount');
    }

    public function updateBankTransaction(Request $request, $id)
    {
        $tx = BankTransaction::findOrFail($id);
        $data = $request->validate([
            'tgl_transaksi' => 'required|date',
            'tipe'          => 'required|in:debit,kredit',
            'nominal'       => 'required|numeric|min:0',
            'mata_uang'     => 'nullable|in:IDR,USD',
            'kurs'          => 'nullable|numeric',
            'kategori'      => 'nullable|string',
            'referensi'     => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);
        $tx->update($data);
        return $tx->load('bankAccount');
    }

    public function destroyBankTransaction($id)
    {
        BankTransaction::findOrFail($id)->delete();
        return response()->json(['message' => 'Transaksi dihapus']);
    }

    // ─── Balances ─────────────────────────────────────────────────────────────

    public function getBalances()
    {
        $accounts = BankAccount::get();
        return $accounts->map(fn($a) => [
            'id'            => $a->id,
            'nama_bank'     => $a->nama_bank,
            'nama_rekening' => $a->nama_rekening,
            'mata_uang'     => $a->mata_uang,
            'saldo_awal'    => (float)$a->saldo_awal,
            'saldo_saat_ini'=> $a->saldo_saat_ini,
        ]);
    }

    // ─── Pembayaran CPO ───────────────────────────────────────────────────────

    public function getPembayaranCpos()
    {
        return PembayaranCpo::with(['kontrakCpo.supplier','bankAccount'])->latest('tgl_bayar')->get();
    }

    public function storePembayaranCpo(Request $request)
    {
        $data = $request->validate([
            'kontrak_cpo_id'     => 'required|exists:kontrak_cpos,id',
            'bank_account_id'    => 'nullable|exists:bank_accounts,id',
            'tgl_bayar'          => 'required|date',
            'nominal'            => 'required|numeric|min:0',
            'mata_uang'          => 'nullable|in:IDR,USD',
            'kurs'               => 'nullable|numeric',
            'keterangan'         => 'nullable|string',
        ]);
        $p = PembayaranCpo::create($data);
        // Create bank transaction
        if (!empty($data['bank_account_id'])) {
            $tx = BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'tgl_transaksi'   => $data['tgl_bayar'],
                'tipe'            => 'debit',
                'nominal'         => $data['nominal'],
                'mata_uang'       => $data['mata_uang'] ?? 'IDR',
                'kurs'            => $data['kurs'] ?? null,
                'kategori'        => 'CPO',
                'referensi'       => 'kontrak_cpo_id:'.$data['kontrak_cpo_id'],
                'keterangan'      => $data['keterangan'] ?? 'Pembayaran CPO',
            ]);
            $p->bank_transaction_id = $tx->id;
            $p->save();
        }
        return $p->load(['kontrakCpo.supplier','bankAccount']);
    }

    public function destroyPembayaranCpo($id)
    {
        PembayaranCpo::findOrFail($id)->delete();
        return response()->json(['message' => 'Pembayaran CPO dihapus']);
    }

    // ─── Pembayaran Sales ─────────────────────────────────────────────────────

    public function getPembayaranPenjualans()
    {
        return PembayaranPenjualan::with(['kontrakPenjualan.buyer','invoice','bankAccount'])->latest('tgl_bayar')->get();
    }

    public function storePembayaranPenjualan(Request $request)
    {
        $data = $request->validate([
            'kontrak_penjualan_id' => 'nullable|exists:kontrak_penjualans,id',
            'invoice_id'           => 'nullable|exists:invoices,id',
            'bank_account_id'      => 'nullable|exists:bank_accounts,id',
            'tgl_bayar'            => 'required|date',
            'nominal'              => 'required|numeric|min:0',
            'mata_uang'            => 'nullable|in:IDR,USD',
            'kurs'                 => 'nullable|numeric',
            'keterangan'           => 'nullable|string',
        ]);
        $p = PembayaranPenjualan::create($data);
        if (!empty($data['bank_account_id'])) {
            $tx = BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'tgl_transaksi'   => $data['tgl_bayar'],
                'tipe'            => 'kredit',
                'nominal'         => $data['nominal'],
                'mata_uang'       => $data['mata_uang'] ?? 'USD',
                'kurs'            => $data['kurs'] ?? null,
                'kategori'        => 'sales',
                'referensi'       => 'invoice_id:'.($data['invoice_id'] ?? '-'),
                'keterangan'      => $data['keterangan'] ?? 'Pembayaran Sales',
            ]);
            $p->bank_transaction_id = $tx->id;
            $p->save();
        }
        return $p->load(['kontrakPenjualan.buyer','invoice','bankAccount']);
    }

    public function destroyPembayaranPenjualan($id)
    {
        PembayaranPenjualan::findOrFail($id)->delete();
        return response()->json(['message' => 'Pembayaran sales dihapus']);
    }

    // ─── Pembayaran Levy Duty ─────────────────────────────────────────────────

    public function getPembayaranLevyDuties()
    {
        return PembayaranLevyDuty::with(['invoice.kontrakPenjualan.buyer','bankAccount'])->latest('tgl_bayar')->get();
    }

    public function storePembayaranLevyDuty(Request $request)
    {
        $data = $request->validate([
            'invoice_id'      => 'nullable|exists:invoices,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'tgl_bayar'       => 'required|date',
            'nominal_usd'     => 'required|numeric|min:0',
            'kurs'            => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string',
        ]);
        $data['nominal_idr'] = (float)$data['nominal_usd'] * (float)$data['kurs'];
        $p = PembayaranLevyDuty::create($data);
        if (!empty($data['bank_account_id'])) {
            BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'tgl_transaksi'   => $data['tgl_bayar'],
                'tipe'            => 'debit',
                'nominal'         => $data['nominal_idr'],
                'mata_uang'       => 'IDR',
                'kurs'            => $data['kurs'],
                'kategori'        => 'levy_duty',
                'keterangan'      => $data['keterangan'] ?? 'Pembayaran Levy Duty',
            ]);
        }
        return $p->load(['invoice.kontrakPenjualan.buyer','bankAccount']);
    }

    public function destroyPembayaranLevyDuty($id)
    {
        PembayaranLevyDuty::findOrFail($id)->delete();
        return response()->json(['message' => 'Pembayaran levy duty dihapus']);
    }

    // ─── Pembayaran Lainnya ───────────────────────────────────────────────────

    public function getPembayaranLainnya()
    {
        return PembayaranLainnya::with('bankAccount')->latest('tgl_bayar')->get();
    }

    public function storePembayaranLainnya(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'tgl_bayar'       => 'required|date',
            'tipe'            => 'required|in:keluar,masuk',
            'nominal'         => 'required|numeric|min:0',
            'mata_uang'       => 'nullable|in:IDR,USD',
            'kurs'            => 'nullable|numeric',
            'kategori'        => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);
        $p = PembayaranLainnya::create($data);
        if (!empty($data['bank_account_id'])) {
            BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'tgl_transaksi'   => $data['tgl_bayar'],
                'tipe'            => $data['tipe'] === 'keluar' ? 'debit' : 'kredit',
                'nominal'         => $data['nominal'],
                'mata_uang'       => $data['mata_uang'] ?? 'IDR',
                'kurs'            => $data['kurs'] ?? null,
                'kategori'        => $data['kategori'] ?? 'lainnya',
                'keterangan'      => $data['keterangan'] ?? '',
            ]);
        }
        return $p->load('bankAccount');
    }

    public function destroyPembayaranLainnya($id)
    {
        PembayaranLainnya::findOrFail($id)->delete();
        return response()->json(['message' => 'Pembayaran lainnya dihapus']);
    }
}
