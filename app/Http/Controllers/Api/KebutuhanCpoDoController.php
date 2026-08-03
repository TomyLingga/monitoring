<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KebutuhanCpoDo;
use Illuminate\Http\Request;

class KebutuhanCpoDoController extends Controller
{
    public function index(Request $request)
    {
        $q = KebutuhanCpoDo::with(['pengiriman.kontrakPenjualan.buyer', 'pengiriman.kontrakPenjualan.produk']);
        if ($request->status) $q->where('status', $request->status);
        if ($request->pengiriman_id) $q->where('pengiriman_id', $request->pengiriman_id);
        return $q->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pengiriman_id'   => 'required|exists:pengiriman_penjualans,id',
            'qty_butuh_kg'    => 'required|numeric|min:0',
            'deadline'        => 'nullable|date',
            'catatan'         => 'nullable|string',
        ]);
        $data['qty_terpenuhi_kg'] = 0;
        $data['status'] = 'pending';

        $item = KebutuhanCpoDo::create($data);

        // Update kebutuhan_cpo_kg di pengiriman
        $pengiriman = $item->pengiriman;
        $pengiriman->kebutuhan_cpo_kg = $pengiriman->kebutuhanCpoDos()->sum('qty_butuh_kg');
        $pengiriman->save();

        return $item->load(['pengiriman.kontrakPenjualan.buyer', 'pengiriman.kontrakPenjualan.produk']);
    }

    public function update(Request $request, $id)
    {
        $item = KebutuhanCpoDo::findOrFail($id);
        $data = $request->validate([
            'qty_butuh_kg'    => 'required|numeric|min:0',
            'deadline'        => 'nullable|date',
            'catatan'         => 'nullable|string',
        ]);
        $item->update($data);
        $item->updateStatus();

        // Recalculate pengiriman
        $pengiriman = $item->pengiriman;
        $pengiriman->kebutuhan_cpo_kg = $pengiriman->kebutuhanCpoDos()->sum('qty_butuh_kg');
        $pengiriman->kebutuhan_cpo_terpenuhi = $pengiriman->kebutuhanCpoDos()->sum('qty_terpenuhi_kg');
        $pengiriman->save();

        return $item->load(['pengiriman.kontrakPenjualan.buyer', 'pengiriman.kontrakPenjualan.produk']);
    }

    public function destroy($id)
    {
        $item = KebutuhanCpoDo::findOrFail($id);
        $pengiriman = $item->pengiriman;
        $item->delete();

        // Recalculate pengiriman
        $pengiriman->kebutuhan_cpo_kg = $pengiriman->kebutuhanCpoDos()->sum('qty_butuh_kg');
        $pengiriman->kebutuhan_cpo_terpenuhi = $pengiriman->kebutuhanCpoDos()->sum('qty_terpenuhi_kg');
        $pengiriman->save();

        return response()->json(['message' => 'Kebutuhan CPO DO dihapus']);
    }

    // Pemenuhan: dipanggil saat Sourcing mengalokasikan incoming CPO ke kebutuhan DO
    public function fulfill(Request $request, $id)
    {
        $item = KebutuhanCpoDo::findOrFail($id);
        $data = $request->validate([
            'qty_pemenuhan_kg' => 'required|numeric|min:0',
        ]);

        $item->qty_terpenuhi_kg = (float)$item->qty_terpenuhi_kg + (float)$data['qty_pemenuhan_kg'];
        $item->updateStatus();

        // Recalculate pengiriman
        $pengiriman = $item->pengiriman;
        $pengiriman->kebutuhan_cpo_terpenuhi = $pengiriman->kebutuhanCpoDos()->sum('qty_terpenuhi_kg');
        $pengiriman->save();

        return $item->load(['pengiriman.kontrakPenjualan.buyer', 'pengiriman.kontrakPenjualan.produk']);
    }
}
