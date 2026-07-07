<?php

namespace App\Http\Controllers;

use App\Models\LevyDuty;
use Illuminate\Http\Request;

class LevyDutyController extends Controller
{
    public function index()
    {
        return response()->json(LevyDuty::with('invoice')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'kapal' => 'nullable|string',
            'tarif' => 'numeric',
            'kurs' => 'numeric',
            'nilai_akhir' => 'numeric',
        ]);
        
        $levy = LevyDuty::create($validated);
        return response()->json($levy->load('invoice'), 201);
    }

    public function update(Request $request, $id)
    {
        $levy = LevyDuty::findOrFail($id);
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'kapal' => 'nullable|string',
            'tarif' => 'numeric',
            'kurs' => 'numeric',
            'nilai_akhir' => 'numeric',
        ]);
        
        $levy->update($validated);
        return response()->json($levy->load('invoice'));
    }

    public function destroy($id)
    {
        $levy = LevyDuty::findOrFail($id);
        $levy->delete();
        return response()->json(null, 204);
    }
}
