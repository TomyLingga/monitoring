<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogisticPeUsage;
use Illuminate\Http\Request;

class LogisticPeUsageController extends Controller
{
    public function index(Request $request)
    {
        $query = LogisticPeUsage::query();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl', [$request->start_date, $request->end_date]);
        }
        return response()->json($query->orderBy('tgl', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qty' => 'required|numeric',
            'tgl' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $item = LogisticPeUsage::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = LogisticPeUsage::findOrFail($id);
        $validated = $request->validate([
            'qty' => 'required|numeric',
            'tgl' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        LogisticPeUsage::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
