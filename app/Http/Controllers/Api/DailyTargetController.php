<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTarget;
use Illuminate\Http\Request;

class DailyTargetController extends Controller
{
    public function index()
    {
        return DailyTarget::orderBy('tgl', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tgl' => 'required|date',
            'target_qty' => 'required|numeric|min:0',
        ]);

        $dailyTarget = DailyTarget::updateOrCreate(
            ['tgl' => $data['tgl']],
            ['target_qty' => $data['target_qty']]
        );

        return response()->json($dailyTarget, 201);
    }

    public function show($id)
    {
        return DailyTarget::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'tgl' => 'required|date',
            'target_qty' => 'required|numeric|min:0',
        ]);

        $dailyTarget = DailyTarget::findOrFail($id);
        
        // Ensure no duplicate dates
        $exists = DailyTarget::where('tgl', $data['tgl'])->where('id', '!=', $id)->exists();
        if ($exists) {
            return response()->json(['message' => 'Tanggal target sudah terdaftar.'], 422);
        }

        $dailyTarget->update($data);
        return $dailyTarget;
    }

    public function destroy($id)
    {
        $dailyTarget = DailyTarget::findOrFail($id);
        $dailyTarget->delete();
        return response()->json(['message' => 'Target harian berhasil dihapus']);
    }
}
