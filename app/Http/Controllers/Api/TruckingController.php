<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'no_do'          => 'nullable|string',
            'qty'            => 'required|numeric',
            'unit_tersedia'  => 'nullable|integer',
            'qty_unit'       => 'nullable|integer',
            'transporter'    => 'nullable|string',
            'destination'    => 'nullable|string',
            'tgl'            => 'required|date',
            'pengiriman_id'  => 'nullable|exists:pengiriman_penjualans,id'
        ]);

        $item = Trucking::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = Trucking::findOrFail($id);
        $validated = $request->validate([
            'no_do'          => 'nullable|string',
            'qty'            => 'required|numeric',
            'unit_tersedia'  => 'nullable|integer',
            'qty_unit'       => 'nullable|integer',
            'transporter'    => 'nullable|string',
            'destination'    => 'nullable|string',
            'tgl'            => 'required|date',
            'pengiriman_id'  => 'nullable|exists:pengiriman_penjualans,id'
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        Trucking::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    /**
     * Ringkasan trucking per transporter untuk tanggal tertentu + grand total harian.
     * GET /truckings/summary?date=YYYY-MM-DD
     */
    public function summary(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));

        $rows = Trucking::whereDate('tgl', $date)
            ->select('transporter', 'destination',
                DB::raw('SUM(qty_unit) as total_unit'),
                DB::raw('SUM(qty) as total_produk'),
                DB::raw('COUNT(*) as jumlah_trip')
            )
            ->groupBy('transporter', 'destination')
            ->orderBy('transporter')
            ->get();

        $grandTotal = [
            'total_unit'   => $rows->sum('total_unit'),
            'total_produk' => $rows->sum('total_produk'),
            'jumlah_trip'  => $rows->sum('jumlah_trip'),
        ];

        return response()->json([
            'date'        => $date,
            'per_transporter' => $rows,
            'grand_total' => $grandTotal,
        ]);
    }
}
