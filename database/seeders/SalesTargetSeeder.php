<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailySalesTarget;
use Carbon\Carbon;

class SalesTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // Generate daily sales targets for the last 30 days and next 7 days
        $targets = [];
        for ($i = -30; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            $targets[] = [
                'tgl' => $date->format('Y-m-d'),
                'target_qty' => rand(800000, 1500000), // Random target between 800k - 1.5M Kg
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DailySalesTarget::insert($targets);
    }
}
