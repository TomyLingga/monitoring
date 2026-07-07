<?php

namespace App\Http\Controllers;

use App\Services\BiKursService;
use Illuminate\Http\Request;

class KursBIController extends Controller
{
    public function __construct(protected BiKursService $biKurs) {}

    public function uka(Request $request)
    {
        $data = $this->biKurs->getKursUka(
            $request->query('mts', 'USD'),
            $request->query('startdate', now()->subDays(150)->format('Y-m-d')),
            $request->query('enddate', now()->format('Y-m-d')),
        );

        return response()->json($data);
    }

    public function jisdor(Request $request)
    {
        $data = $this->biKurs->getKursJisdor(
            $request->query('mts', 'USD'),
            $request->query('startDate', now()->subDays(150)->format('Y-m-d')),
            $request->query('endDate', now()->format('Y-m-d')),
        );

        return response()->json($data);
    }
}
