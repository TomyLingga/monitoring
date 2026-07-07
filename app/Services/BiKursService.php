<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BiKursService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('URL_KURS_BI', 'https://www.bi.go.id/biwebservice/wskursbi.asmx');
    }

    /**
     * Kurs UKA (Uang Kertas Asing) - transaksi bank
     */
    public function getKursUka(string $mts, string $startdate, string $enddate): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8',
                    'Referer'         => 'https://www.bi.go.id/',
                ])
                ->timeout(30)
                ->get("{$this->baseUrl}/getSubKursAsing3", [
                    'mts'       => $mts,
                    'startdate' => $startdate,
                    'enddate'   => $enddate,
                ]);

            if (!$response->successful()) return [];

            $xml = simplexml_load_string($response->body());
            if ($xml === false) return [];

            $xml->registerXPathNamespace('diffgr', 'urn:schemas-microsoft-com:xml-diffgram-v1');
            $tables = $xml->xpath('//diffgr:diffgram//*[local-name()="Table"]');

            $data = [];
            foreach ($tables as $table) {
                $data[] = [
                    'id'        => (int)    $table->id_subkursasing,
                    'nilai'     => (float)  $table->nil_subkursasing,
                    'beli'      => (float)  $table->beli_subkursasing,
                    'jual'      => (float)  $table->jual_subkursasing,
                    'tanggal'   => (string) $table->tgl_subkursasing,
                    'mata_uang' => trim((string) $table->mts_subkursasing),
                ];
            }

            return $data;
        } catch (\Exception $e) {
            \Log::warning('BI UKA fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Kurs JISDOR - kurs referensi Bank Indonesia
     * NOTE: JISDOR endpoint returns the same XML fields as UKA (_subkursasing suffix),
     * with beli_subkursasing == jual_subkursasing == JISDOR rate.
     */
    public function getKursJisdor(string $mts, string $startDate, string $endDate): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8',
                    'Referer'         => 'https://www.bi.go.id/',
                ])
                ->timeout(30)
                ->get("{$this->baseUrl}/getSubKursJisdor3", [
                    'mts'       => $mts,
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                ]);

            if (!$response->successful()) return [];

            $xml = simplexml_load_string($response->body());
            if ($xml === false) return [];

            $xml->registerXPathNamespace('diffgr', 'urn:schemas-microsoft-com:xml-diffgram-v1');
            $tables = $xml->xpath('//diffgr:diffgram//*[local-name()="Table"]');

            $data = [];
            foreach ($tables as $table) {
                // JISDOR uses the same _subkursasing field names as UKA.
                // For JISDOR, beli == jual == JISDOR rate.
                $jisdorRate = (float) $table->beli_subkursasing;
                $data[] = [
                    'id'        => (int)    $table->id_subkursasing,
                    'nilai'     => $jisdorRate,   // JISDOR rate stored in nilai for frontend
                    'beli'      => null,           // Not applicable for JISDOR
                    'jual'      => null,           // Not applicable for JISDOR
                    'tanggal'   => (string) $table->tgl_subkursasing,
                    'mata_uang' => trim((string) $table->mts_subkursasing),
                ];
            }

            return $data;
        } catch (\Exception $e) {
            \Log::warning('BI JISDOR fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}

