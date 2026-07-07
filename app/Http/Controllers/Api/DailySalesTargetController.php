<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailySalesTarget;
use Illuminate\Http\Request;

class DailySalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = DailySalesTarget::orderBy('tgl', 'asc');
        if ($request->start_date) {
            $query->where('tgl', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('tgl', '<=', $request->end_date);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tgl' => 'required|date',
            'target_qty' => 'required|numeric'
        ]);

        return DailySalesTarget::create($data);
    }

    public function show(DailySalesTarget $dailySalesTarget)
    {
        return $dailySalesTarget;
    }

    public function update(Request $request, DailySalesTarget $dailySalesTarget)
    {
        $data = $request->validate([
            'tgl' => 'required|date',
            'target_qty' => 'required|numeric'
        ]);

        $dailySalesTarget->update($data);
        return $dailySalesTarget;
    }

    public function destroy(DailySalesTarget $dailySalesTarget)
    {
        $dailySalesTarget->delete();
        return response()->json(['message' => 'Target penjualan dihapus']);
    }
}
