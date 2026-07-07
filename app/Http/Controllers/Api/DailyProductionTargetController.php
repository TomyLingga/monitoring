<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyProductionTarget;
use Illuminate\Http\Request;

class DailyProductionTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyProductionTarget::orderBy('tgl', 'desc');

        if ($request->start_date) $query->where('tgl', '>=', $request->start_date);
        if ($request->end_date)   $query->where('tgl', '<=', $request->end_date);
        if ($request->jenis)      $query->where('jenis', $request->jenis);

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl'        => 'required|date',
            'target_qty' => 'required|numeric|min:0',
            'jenis'      => 'nullable|string|in:refinery,fraksinasi,packaging',
        ]);

        $jenis = $request->jenis ?? 'refinery';

        $target = DailyProductionTarget::updateOrCreate(
            ['tgl' => $request->tgl, 'jenis' => $jenis],
            [
                'target_qty' => $request->target_qty,
                'satuan'     => $request->satuan ?? 'Kg',
                'catatan'    => $request->catatan,
            ]
        );

        return response()->json($target, 201);
    }

    public function show($id)
    {
        return response()->json(DailyProductionTarget::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $target = DailyProductionTarget::findOrFail($id);
        $target->update($request->only(['tgl', 'jenis', 'target_qty', 'satuan', 'catatan']));
        return response()->json($target);
    }

    public function destroy($id)
    {
        DailyProductionTarget::findOrFail($id)->delete();
        return response()->json(['message' => 'Target produksi dihapus']);
    }
}
