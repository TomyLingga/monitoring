<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PembayaranCpo;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranCpoController extends Controller
{
    public function index() { return PembayaranCpo::with('kontrakCpo.supplier')->latest('tgl_bayar')->get(); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'nominal' => 'required|numeric|min:0', 'tgl_bayar' => 'required|date',
            'metode_bayar' => 'nullable|string', 'bukti_bayar' => 'nullable|string', 'catatan' => 'nullable|string',
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        return DB::transaction(function () use ($data) {
            $pembayaran = PembayaranCpo::create($data);

            BankTransaction::create([
                'bank_account_id' => $data['bank_account_id'],
                'type' => 'out',
                'amount' => $data['nominal'],
                'transaction_date' => $data['tgl_bayar'],
                'description' => 'Pembayaran CPO Kontrak ID ' . $data['kontrak_cpo_id'] . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                'reference_type' => PembayaranCpo::class,
                'reference_id' => $pembayaran->id,
            ]);

            return $pembayaran->load('kontrakCpo');
        });
    }
    public function show(PembayaranCpo $pembayaranCpo) { return $pembayaranCpo->load('kontrakCpo.supplier'); }
    public function update(Request $request, PembayaranCpo $pembayaranCpo)
    {
        $data = $request->validate([
            'kontrak_cpo_id' => 'required|exists:kontrak_cpos,id',
            'nominal' => 'required|numeric|min:0', 'tgl_bayar' => 'required|date',
            'metode_bayar' => 'nullable|string', 'bukti_bayar' => 'nullable|string', 'catatan' => 'nullable|string',
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        return DB::transaction(function () use ($data, $pembayaranCpo) {
            $pembayaranCpo->update($data);

            $txn = BankTransaction::where('reference_type', PembayaranCpo::class)->where('reference_id', $pembayaranCpo->id)->first();
            if ($txn) {
                $txn->update([
                    'bank_account_id' => $data['bank_account_id'],
                    'amount' => $data['nominal'],
                    'transaction_date' => $data['tgl_bayar'],
                    'description' => 'Pembayaran CPO Kontrak ID ' . $data['kontrak_cpo_id'] . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                ]);
            } else {
                BankTransaction::create([
                    'bank_account_id' => $data['bank_account_id'],
                    'type' => 'out',
                    'amount' => $data['nominal'],
                    'transaction_date' => $data['tgl_bayar'],
                    'description' => 'Pembayaran CPO Kontrak ID ' . $data['kontrak_cpo_id'] . ($data['catatan'] ? ' - ' . $data['catatan'] : ''),
                    'reference_type' => PembayaranCpo::class,
                    'reference_id' => $pembayaranCpo->id,
                ]);
            }

            return $pembayaranCpo->load('kontrakCpo');
        });
    }
    public function destroy(PembayaranCpo $pembayaranCpo) 
    { 
        return DB::transaction(function () use ($pembayaranCpo) {
            BankTransaction::where('reference_type', PembayaranCpo::class)->where('reference_id', $pembayaranCpo->id)->delete();
            $pembayaranCpo->delete(); 
            return response()->json(['message' => 'Pembayaran berhasil dihapus']);
        });
    }
}
