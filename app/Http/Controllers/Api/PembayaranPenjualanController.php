<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranPenjualan;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranPenjualanController extends Controller
{
    public function index()
    {
        return PembayaranPenjualan::with(['kontrakPenjualan.buyer'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'invoice_id'           => 'nullable|exists:invoices,id',
            'bank_account_id'      => 'required|exists:bank_accounts,id',
            'nominal'              => 'required|numeric|min:0',
            'tgl_bayar'            => 'required|date',
            'catatan'              => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $pembayaran = PembayaranPenjualan::create($data);

            BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'type' => 'in',
                'amount' => $data['nominal'],
                'transaction_date' => $data['tgl_bayar'],
                'description' => 'Penerimaan Pembayaran Penjualan ' . ($data['invoice_id'] ? 'Invoice ID ' . $data['invoice_id'] : 'Kontrak ID ' . $data['kontrak_penjualan_id']) . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                'reference_type' => PembayaranPenjualan::class,
                'reference_id' => $pembayaran->id,
            ]);

            return $pembayaran->load('kontrakPenjualan.buyer');
        });
    }

    public function show($id)
    {
        return PembayaranPenjualan::with('kontrakPenjualan.buyer')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPenjualan::findOrFail($id);
        $data = $request->validate([
            'kontrak_penjualan_id' => 'required|exists:kontrak_penjualans,id',
            'invoice_id'           => 'nullable|exists:invoices,id',
            'bank_account_id'      => 'required|exists:bank_accounts,id',
            'nominal'              => 'required|numeric|min:0',
            'tgl_bayar'            => 'required|date',
            'catatan'              => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $pembayaran) {
            $pembayaran->update($data);

            $txn = BankTransaction::where('reference_type', PembayaranPenjualan::class)->where('reference_id', $pembayaran->id)->first();
            if ($txn) {
                $txn->update([
                    'bank_account_id' => $data['bank_account_id'],
                    'amount' => $data['nominal'],
                    'transaction_date' => $data['tgl_bayar'],
                    'description' => 'Penerimaan Pembayaran Penjualan ' . ($data['invoice_id'] ? 'Invoice ID ' . $data['invoice_id'] : 'Kontrak ID ' . $data['kontrak_penjualan_id']) . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                ]);
            } else {
                BankTransaction::create([
                    'bank_account_id' => $data['bank_account_id'],
                    'type' => 'in',
                    'amount' => $data['nominal'],
                    'transaction_date' => $data['tgl_bayar'],
                    'description' => 'Penerimaan Pembayaran Penjualan ' . ($data['invoice_id'] ? 'Invoice ID ' . $data['invoice_id'] : 'Kontrak ID ' . $data['kontrak_penjualan_id']) . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                    'reference_type' => PembayaranPenjualan::class,
                    'reference_id' => $pembayaran->id,
                ]);
            }

            return $pembayaran->load('kontrakPenjualan.buyer');
        });
    }

    public function destroy($id)
    {
        $pembayaran = PembayaranPenjualan::findOrFail($id);
        
        return DB::transaction(function () use ($pembayaran) {
            BankTransaction::where('reference_type', PembayaranPenjualan::class)->where('reference_id', $pembayaran->id)->delete();
            $pembayaran->delete();
            return response()->json(['message' => 'Pembayaran penjualan berhasil dihapus']);
        });
    }
}
