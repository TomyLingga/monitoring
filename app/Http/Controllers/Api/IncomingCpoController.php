<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{IncomingCpo, StokProduk, KebutuhanCpoDo};
use App\Models\MasterProduk;
use Illuminate\Http\Request;

class IncomingCpoController extends Controller {
    public function index() {
        return IncomingCpo::with(['kontrakCpo.supplier','storage','kebutuhanCpoDo'])->latest('tgl_terima')->get();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'kontrak_cpo_id'       => 'nullable|exists:kontrak_cpos,id',
            'kebutuhan_cpo_do_id'  => 'nullable|exists:kebutuhan_cpo_dos,id',
            'storage_id'           => 'required|exists:storages,id',
            'nomor_do'             => 'nullable|string',
            'qty_order'            => 'nullable|numeric|min:0',
            'qty_terima'           => 'required|numeric|min:0',
            'harga_per_kg'         => 'nullable|numeric|min:0',
            'tgl_rencana'          => 'nullable|date',
            'tgl_terima'           => 'required|date',
            'no_dok'               => 'nullable|string',
            'keterangan'           => 'nullable|string',
        ]);
        $incoming = IncomingCpo::create($data);

        // Update stok CPO
        $cpoProduk = MasterProduk::where('kategori','CPO')->first();
        if ($cpoProduk) {
            $stok = StokProduk::firstOrCreate(
                ['storage_id'=>$data['storage_id'],'produk_id'=>$cpoProduk->id],
                ['qty'=>0,'mata_uang'=>'IDR']
            );
            $stok->qty = (float)$stok->qty + (float)$data['qty_terima'];
            if (!empty($data['harga_per_kg'])) $stok->harga_satuan = $data['harga_per_kg'];
            $stok->tgl_update = now();
            $stok->save();
        }

        // Auto-fulfill kebutuhan CPO DO jika ada
        if (!empty($data['kebutuhan_cpo_do_id'])) {
            $kebutuhan = KebutuhanCpoDo::find($data['kebutuhan_cpo_do_id']);
            if ($kebutuhan) {
                $kebutuhan->qty_terpenuhi_kg = (float)$kebutuhan->qty_terpenuhi_kg + (float)$data['qty_terima'];
                $kebutuhan->updateStatus();

                // Update pengiriman aggregate
                $pengiriman = $kebutuhan->pengiriman;
                if ($pengiriman) {
                    $pengiriman->kebutuhan_cpo_terpenuhi = $pengiriman->kebutuhanCpoDos()->sum('qty_terpenuhi_kg');
                    $pengiriman->save();
                }
            }
        }

        return $incoming->load(['kontrakCpo.supplier','storage','kebutuhanCpoDo']);
    }

    public function show(IncomingCpo $incomingCpo) {
        return $incomingCpo->load(['kontrakCpo.supplier','storage','kebutuhanCpoDo']);
    }

    public function update(Request $request, IncomingCpo $incomingCpo) {
        $data = $request->validate([
            'kontrak_cpo_id'       => 'nullable|exists:kontrak_cpos,id',
            'kebutuhan_cpo_do_id'  => 'nullable|exists:kebutuhan_cpo_dos,id',
            'storage_id'           => 'required|exists:storages,id',
            'nomor_do'             => 'nullable|string',
            'qty_order'            => 'nullable|numeric|min:0',
            'qty_terima'           => 'required|numeric|min:0',
            'harga_per_kg'         => 'nullable|numeric|min:0',
            'tgl_rencana'          => 'nullable|date',
            'tgl_terima'           => 'required|date',
            'no_dok'               => 'nullable|string',
            'keterangan'           => 'nullable|string',
        ]);
        $incomingCpo->update($data);
        return $incomingCpo->load(['kontrakCpo.supplier','storage','kebutuhanCpoDo']);
    }

    public function destroy(IncomingCpo $incomingCpo) {
        $incomingCpo->delete();
        return response()->json(['message'=>'OK']);
    }
}
