<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trucking;
use App\Models\LogisticPeTarget;
use App\Models\LogisticAdditionalPe;
use App\Models\LogisticPeUsage;
use Carbon\Carbon;

class LogisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Trucking
        Trucking::insert([
            ['no_do' => 'DO-2026-001', 'qty' => 250000, 'unit_tersedia' => 10, 'transporter' => 'PT Logistik Makmur', 'tgl' => $today->copy()->subDays(2)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['no_do' => 'DO-2026-002', 'qty' => 150000, 'unit_tersedia' => 6, 'transporter' => 'PT Lintas Benua', 'tgl' => $today->copy()->subDays(1)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['no_do' => 'DO-2026-003', 'qty' => 300000, 'unit_tersedia' => 12, 'transporter' => 'PT Cepat Express', 'tgl' => $today->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Target PE Bulanan
        LogisticPeTarget::insert([
            ['jumlah' => 5000000, 'tgl' => $today->copy()->startOfMonth()->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Tambahan PE
        LogisticAdditionalPe::insert([
            ['qty' => 1000000, 'tgl' => $today->copy()->subDays(10)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['qty' => 500000, 'tgl' => $today->copy()->subDays(3)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Pemakaian PE
        LogisticPeUsage::insert([
            ['qty' => 250000, 'tgl' => $today->copy()->subDays(5)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['qty' => 150000, 'tgl' => $today->copy()->subDays(4)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['qty' => 300000, 'tgl' => $today->copy()->subDays(2)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['qty' => 100000, 'tgl' => $today->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
