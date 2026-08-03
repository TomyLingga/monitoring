<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KursPajakCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KursPajakController extends Controller
{
    /**
     * GET /api/kurs-pajak?date=YYYY-MM-DD
     * Jika tidak ada date, ambil yang paling baru.
     */
    public function index(Request $request)
    {
        $date = $request->get('date');

        if ($date) {
            $cached = KursPajakCache::whereDate('tanggal', $date)
                ->orWhere(fn($q) => $q->whereDate('periode_mulai','<=',$date)->whereDate('periode_akhir','>=',$date))
                ->latest('tanggal')
                ->first();
        } else {
            $cached = KursPajakCache::latest('tanggal')->first();
        }

        if ($cached && $cached->fetched_at > now()->subDays(7)) {
            return response()->json([
                'source'       => 'cache',
                'tanggal'      => $cached->tanggal,
                'periode_mulai'=> $cached->periode_mulai,
                'periode_akhir'=> $cached->periode_akhir,
                'kurs'         => $cached->raw_json,
                'usd'          => $cached->kurs_usd,
            ]);
        }

        // Fetch dari Kemenkeu
        $result = $this->fetchFromKemenkeu($date);
        if (!$result) {
            // Kembalikan cache lama jika ada
            if ($cached) {
                return response()->json([
                    'source' => 'cache_stale',
                    'tanggal' => $cached->tanggal,
                    'kurs'   => $cached->raw_json,
                    'usd'    => $cached->kurs_usd,
                ]);
            }
            return response()->json(['message' => 'Gagal mengambil kurs KMK'], 503);
        }

        return response()->json(array_merge($result, ['source' => 'live']));
    }

    /**
     * GET /api/kurs-pajak/history?weeks=8
     * Mengembalikan history kurs KMK n minggu terakhir
     */
    public function history(Request $request)
    {
        $weeks = min((int)$request->get('weeks', 8), 26);
        $records = KursPajakCache::orderBy('tanggal','desc')->take($weeks)->get()
            ->map(fn($r) => [
                'tanggal'       => $r->tanggal,
                'periode_mulai' => $r->periode_mulai,
                'periode_akhir' => $r->periode_akhir,
                'usd'           => $r->kurs_usd,
                'kurs'          => $r->raw_json,
            ]);
        return response()->json($records);
    }

    private function fetchFromKemenkeu(?string $date): ?array
    {
        try {
            // Kemenkeu KURS API
            $url = 'https://fiskal.kemenkeu.go.id/api/v1/kurs-pajak';
            if ($date) {
                $url .= '?date='.urlencode($date);
            }

            $response = Http::timeout(10)->get($url);
            if (!$response->successful()) return null;

            $json = $response->json();

            // Parse response — Kemenkeu format can vary
            $data     = $json['data'] ?? $json;
            $tanggal  = $json['tanggal'] ?? $json['periode_dari'] ?? now()->format('Y-m-d');
            $periodeA = $json['periode_dari'] ?? $tanggal;
            $periodeB = $json['periode_sampai'] ?? $tanggal;

            // Normalize kurs array
            $kurs = [];
            foreach (($data['kurs'] ?? $data ?? []) as $item) {
                $kurs[] = [
                    'kode'         => $item['kode_mata_uang'] ?? $item['kode'] ?? '',
                    'nama'         => $item['nama_mata_uang'] ?? $item['nama'] ?? '',
                    'nilai_jual'   => (float)($item['nilai_jual'] ?? $item['kurs'] ?? 0),
                    'nilai_beli'   => (float)($item['nilai_beli'] ?? 0),
                    'nilai_tengah' => (float)($item['nilai_tengah'] ?? ($item['kurs'] ?? 0)),
                ];
            }

            if (empty($kurs)) return null;

            // Cache it
            $cache = KursPajakCache::updateOrCreate(
                ['tanggal' => $tanggal],
                [
                    'periode_mulai' => $periodeA,
                    'periode_akhir' => $periodeB,
                    'raw_json'      => $kurs,
                    'fetched_at'    => now(),
                ]
            );

            $usd = collect($kurs)->first(fn($k) => strtoupper($k['kode']) === 'USD');
            return [
                'tanggal'       => $tanggal,
                'periode_mulai' => $periodeA,
                'periode_akhir' => $periodeB,
                'kurs'          => $kurs,
                'usd'           => $usd ? ($usd['nilai_tengah'] ?? $usd['nilai_jual']) : null,
            ];
        } catch (\Exception $e) {
            \Log::error('KursPajak fetch error: '.$e->getMessage());
            return null;
        }
    }
}
