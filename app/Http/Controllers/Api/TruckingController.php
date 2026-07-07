<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trucking;
use Illuminate\Http\Request;

class TruckingController extends Controller
{
    public function index(Request $request)
    {
        $query = Trucking::query();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl', [$request->start_date, $request->end_date]);
        }
        return response()->json($query->orderBy('tgl', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_do' => 'required|string|unique:truckings,no_do',
            'qty' => 'required|numeric',
            'unit_tersedia' => 'required|integer',
            'transporter' => 'nullable|string',
            'tgl' => 'required|date',
            'pengiriman_id' => 'nullable|exists:pengiriman_penjualans,id'
        ]);

        $item = Trucking::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = Trucking::findOrFail($id);
        $validated = $request->validate([
            'no_do' => 'required|string|unique:truckings,no_do,' . $id,
            'qty' => 'required|numeric',
            'unit_tersedia' => 'required|integer',
            'transporter' => 'nullable|string',
            'tgl' => 'required|date',
            'pengiriman_id' => 'nullable|exists:pengiriman_penjualans,id'
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        Trucking::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
