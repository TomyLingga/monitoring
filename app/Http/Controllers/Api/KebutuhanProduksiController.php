<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KebutuhanProduksi;
use Illuminate\Http\Request;

class KebutuhanProduksiController extends Controller
{
    public function index()
    {
        return KebutuhanProduksi::with([
            'pengiriman.kontrakPenjualan.buyer',
            'pengiriman.kontrakPenjualan.produk',
            'pengiriman.jadwalKapal',
            'produk'
        ])->get()->map(fn($k) => array_merge($k->toArray(), ['outstanding' => $k->outstanding]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pengiriman_id'   => 'required|exists:pengiriman_penjualans,id',
            'produk_id'       => 'required|exists:master_produks,id',
            'qty_butuh'       => 'required|numeric|min:0',
            'qty_terproduksi' => 'nullable|numeric|min:0',
            'deadline'        => 'nullable|date',
            'status'          => 'nullable|in:pending,partial,done',
            'keterangan'      => 'nullable|string',
        ]);
        return KebutuhanProduksi::create($data)->load(['pengiriman.kontrakPenjualan.buyer','produk']);
    }

    public function update(Request $request, KebutuhanProduksi $kebutuhanProduksi)
    {
        $data = $request->validate([
            'qty_butuh'       => 'required|numeric|min:0',
            'qty_terproduksi' => 'nullable|numeric|min:0',
            'deadline'        => 'nullable|date',
            'status'          => 'nullable|in:pending,partial,done',
            'keterangan'      => 'nullable|string',
        ]);
        // Auto status
        if (isset($data['qty_terproduksi'])) {
            if ((float)$data['qty_terproduksi'] >= (float)($data['qty_butuh'] ?? $kebutuhanProduksi->qty_butuh))
                $data['status'] = 'done';
            elseif ((float)$data['qty_terproduksi'] > 0)
                $data['status'] = 'partial';
        }
        $kebutuhanProduksi->update($data);
        return $kebutuhanProduksi->load(['pengiriman.kontrakPenjualan.buyer','produk']);
    }

    public function destroy(KebutuhanProduksi $kebutuhanProduksi)
    {
        $kebutuhanProduksi->delete();
        return response()->json(['message' => 'Kebutuhan produksi dihapus']);
    }
}
