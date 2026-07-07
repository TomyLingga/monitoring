<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogisticPeTarget;
use Illuminate\Http\Request;

class LogisticPeTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = LogisticPeTarget::query();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tgl', [$request->start_date, $request->end_date]);
        }
        return response()->json($query->orderBy('tgl', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric',
            'tgl' => 'required|date'
        ]);

        $item = LogisticPeTarget::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = LogisticPeTarget::findOrFail($id);
        $validated = $request->validate([
            'jumlah' => 'required|numeric',
            'tgl' => 'required|date'
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        LogisticPeTarget::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
