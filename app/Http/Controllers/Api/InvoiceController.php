<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Invoice, PengirimanPenjualan};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with([
            'kontrakPenjualan.buyer',
            'kontrakPenjualan.produk',
            'pengiriman.jadwalKapal',
            'pembayaranPenjualans'
        ])->latest('tgl_invoice')->get();
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'pengiriman_id'        => 'nullable|exists:pengiriman_penjualans,id',
            'kontrak_penjualan_id' => 'nullable|exists:kontrak_penjualans,id',
            'nomor_invoice'        => 'required|string|unique:invoices',
            'dpp'                  => 'nullable|numeric|min:0',
            'ppn'                  => 'nullable|numeric|min:0',
            'total'                => 'nullable|numeric|min:0',
            'qty'                  => 'nullable|numeric|min:0',
            'harga_satuan'         => 'nullable|numeric|min:0',
            'mata_uang'            => 'nullable|in:IDR,USD',
            'kurs_konversi'        => 'nullable|numeric',
            'levy_amount'          => 'nullable|numeric',
            'levy_kurs'            => 'nullable|numeric',
            'tgl_invoice'          => 'required|date',
            'tgl_jatuh_tempo'      => 'nullable|date',
            'status'               => 'nullable|in:unpaid,paid,draft,terbit,lunas,batal',
            'keterangan'           => 'nullable|string',
        ]);

        // Auto-link to kontrak from pengiriman if pengiriman_id is supplied
        if (!empty($d['pengiriman_id']) && empty($d['kontrak_penjualan_id'])) {
            $p = PengirimanPenjualan::find($d['pengiriman_id']);
            if ($p) {
                $d['kontrak_penjualan_id'] = $p->kontrak_penjualan_id;
                if (empty($d['qty'])) $d['qty'] = $p->qty_order;
            }
        }

        if (empty($d['dpp']) && isset($d['qty'], $d['harga_satuan'])) {
            $d['dpp'] = (float)$d['qty'] * (float)$d['harga_satuan'];
        }
        if (empty($d['total'])) {
            $d['total'] = (float)($d['dpp'] ?? 0) + (float)($d['ppn'] ?? 0);
        }
        $d['nilai_invoice'] = $d['total'];

        return Invoice::create($d)->load(['kontrakPenjualan.buyer', 'pengiriman.jadwalKapal']);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['kontrakPenjualan.buyer', 'kontrakPenjualan.produk', 'pengiriman.jadwalKapal', 'pembayaranPenjualans']);
    }

    public function update(Request $r, Invoice $invoice)
    {
        $d = $r->validate([
            'pengiriman_id'        => 'nullable|exists:pengiriman_penjualans,id',
            'kontrak_penjualan_id' => 'nullable|exists:kontrak_penjualans,id',
            'nomor_invoice'        => 'required|string|unique:invoices,nomor_invoice,' . $invoice->id,
            'dpp'                  => 'nullable|numeric|min:0',
            'ppn'                  => 'nullable|numeric|min:0',
            'total'                => 'nullable|numeric|min:0',
            'qty'                  => 'nullable|numeric|min:0',
            'harga_satuan'         => 'nullable|numeric|min:0',
            'mata_uang'            => 'nullable|in:IDR,USD',
            'kurs_konversi'        => 'nullable|numeric',
            'levy_amount'          => 'nullable|numeric',
            'levy_kurs'            => 'nullable|numeric',
            'tgl_invoice'          => 'required|date',
            'tgl_jatuh_tempo'      => 'nullable|date',
            'status'               => 'nullable|in:unpaid,paid,draft,terbit,lunas,batal',
            'keterangan'           => 'nullable|string',
        ]);

        if (empty($d['dpp']) && isset($d['qty'], $d['harga_satuan'])) {
            $d['dpp'] = (float)$d['qty'] * (float)$d['harga_satuan'];
        }
        if (empty($d['total'])) {
            $d['total'] = (float)($d['dpp'] ?? 0) + (float)($d['ppn'] ?? 0);
        }
        $d['nilai_invoice'] = $d['total'];

        $invoice->update($d);
        return $invoice->load(['kontrakPenjualan.buyer', 'pengiriman.jadwalKapal']);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'OK']);
    }
}
