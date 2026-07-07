<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function bulkStore(\Illuminate\Http\Request $request)
    {
        $request->validate(['data' => 'required|array']);
        
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $results = [];
                foreach ($request->data as $index => $item) {
                    try {
                        $itemRequest = $request->duplicate();
                        $itemRequest->replace($item);
                        $itemRequest->setMethod('POST');
                        
                        $response = $this->store($itemRequest);
                        
                        if ($response->getStatusCode() !== 201 && $response->getStatusCode() !== 200) {
                            $content = json_decode($response->getContent(), true);
                            $msg = $content['message'] ?? 'Unknown error';
                            throw new \Exception("Baris " . ($index + 1) . " gagal: " . $msg);
                        }
                        
                        $results[] = json_decode($response->getContent(), true);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $errors = $e->validator->errors()->first();
                        throw new \Exception("Baris " . ($index + 1) . " gagal: " . $errors);
                    } catch (\Illuminate\Database\QueryException $e) {
                        if (in_array($e->getCode(), [23505, 1062, '23505'])) {
                            throw new \Exception("Baris " . ($index + 1) . " gagal: Data duplikat (Unique Constraint). Pastikan tidak ada data ganda.");
                        }
                        throw new \Exception("Baris " . ($index + 1) . " gagal: Error Database (" . $e->getCode() . ").");
                    }
                }
                return response()->json($results, 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
