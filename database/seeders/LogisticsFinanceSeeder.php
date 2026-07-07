<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogisticsFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Truckings
        $truckingsData = [];
        for ($i = 0; $i < 10; $i++) {
            $truckingsData[] = [
                'tgl' => $today->copy()->subDays(rand(1, 30))->format('Y-m-d'),
                'no_do' => 'DO-' . rand(1000, 9999),
                'qty' => rand(10000, 50000),
                'unit_tersedia' => rand(5, 20),
                'transporter' => 'PT Transporter ' . chr(rand(65, 90)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('truckings')->insert($truckingsData);

        // 2. PE Targets
        $peTargetsData = [];
        for ($i = 0; $i < 5; $i++) {
            $peTargetsData[] = [
                'jumlah' => rand(1000000, 5000000),
                'tgl' => $today->copy()->subDays(rand(1, 30))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('logistic_pe_targets')->insert($peTargetsData);

        // 3. PE Additionals
        $peAdditionalsData = [];
        for ($i = 0; $i < 5; $i++) {
            $peAdditionalsData[] = [
                'qty' => rand(100000, 500000),
                'keterangan' => 'Tambahan kuota ekspor',
                'tgl' => $today->copy()->subDays(rand(1, 30))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('logistic_additional_pes')->insert($peAdditionalsData);

        // 4. PE Usages
        $peUsagesData = [];
        for ($i = 0; $i < 10; $i++) {
            $peUsagesData[] = [
                'qty' => rand(50000, 200000),
                'keterangan' => 'Pengiriman MV Kapuas ' . rand(1, 10),
                'tgl' => $today->copy()->subDays(rand(1, 30))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('logistic_pe_usages')->insert($peUsagesData);

        // 5. Bank Accounts
        $bankAccountId1 = DB::table('bank_accounts')->insertGetId([
            'bank_name' => 'BCA',
            'account_name' => 'PT Makmur Sejahtera',
            'account_number' => '876543210',
            'currency' => 'IDR',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bankAccountId2 = DB::table('bank_accounts')->insertGetId([
            'bank_name' => 'Mandiri',
            'account_name' => 'PT Makmur Sejahtera',
            'account_number' => '123456789',
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Bank Transactions (Initial Deposit)
        DB::table('bank_transactions')->insert([
            [
                'bank_account_id' => $bankAccountId1,
                'type' => 'in',
                'amount' => 500000000, // 500 Juta
                'transaction_date' => $today->copy()->subDays(15)->format('Y-m-d'),
                'description' => 'Setoran Modal Awal',
                'reference_type' => null,
                'reference_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bank_account_id' => $bankAccountId2,
                'type' => 'in',
                'amount' => 50000, // 50K USD
                'transaction_date' => $today->copy()->subDays(10)->format('Y-m-d'),
                'description' => 'Deposit USD',
                'reference_type' => null,
                'reference_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Calculate Daily Balance for Bank Accounts
        $this->calculateDailyBalance($bankAccountId1, $today->copy()->subDays(15)->format('Y-m-d'));
        $this->calculateDailyBalance($bankAccountId2, $today->copy()->subDays(10)->format('Y-m-d'));

        // Dummy Supplier
        $supplierId = DB::table('suppliers')->first()->id ?? DB::table('suppliers')->insertGetId([
            'nama' => 'PT Dummy Supplier',
            'alamat' => 'Jakarta',
            'pic' => 'Budi',
            'no_hp' => '08123456789',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 7. Payments
        $paymentId1 = DB::table('payments')->insertGetId([
            'supplier_id' => $supplierId,
            'bank_account_id' => $bankAccountId1,
            'amount' => 150000000,
            'fr_number' => 'FR-101',
            'job_object' => 'Sewa Kapal',
            'payment_date' => $today->copy()->subDays(5)->format('Y-m-d'),
            'status' => 'proses',
            'created_at' => $today->copy()->subDays(5)->format('Y-m-d H:i:s'),
            'updated_at' => now(),
        ]);

        $paymentId2 = DB::table('payments')->insertGetId([
            'supplier_id' => $supplierId,
            'bank_account_id' => $bankAccountId1,
            'amount' => 25000000,
            'fr_number' => 'FR-102',
            'job_object' => 'Maintenance Gudang',
            'payment_date' => $today->copy()->subDays(20)->format('Y-m-d'),
            'status' => 'selesai',
            'created_at' => $today->copy()->subDays(20)->format('Y-m-d H:i:s'),
            'updated_at' => now(),
        ]);

        // 8. Payment Histories
        DB::table('payment_histories')->insert([
            'payment_id' => $paymentId1,
            'bank_account_id' => $bankAccountId1,
            'amount' => 50000000, // Cicilan 50jt
            'payment_date' => $today->copy()->subDays(3)->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('bank_transactions')->insert([
            'bank_account_id' => $bankAccountId1,
            'type' => 'out',
            'amount' => 50000000,
            'transaction_date' => $today->copy()->subDays(3)->format('Y-m-d'),
            'description' => 'Pembayaran Tagihan FR-101 (Cicilan)',
            'reference_type' => 'App\Models\PaymentHistory',
            'reference_id' => DB::getPdo()->lastInsertId(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->calculateDailyBalance($bankAccountId1, $today->copy()->subDays(3)->format('Y-m-d'));


        DB::table('payment_histories')->insert([
            'payment_id' => $paymentId2,
            'bank_account_id' => $bankAccountId1,
            'amount' => 25000000, // Lunas 25jt
            'payment_date' => $today->copy()->subDays(19)->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bank_transactions')->insert([
            'bank_account_id' => $bankAccountId1,
            'type' => 'out',
            'amount' => 25000000,
            'transaction_date' => $today->copy()->subDays(19)->format('Y-m-d'),
            'description' => 'Pembayaran Tagihan FR-102 (Lunas)',
            'reference_type' => 'App\Models\PaymentHistory',
            'reference_id' => DB::getPdo()->lastInsertId(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->calculateDailyBalance($bankAccountId1, $today->copy()->subDays(19)->format('Y-m-d'));

    }

    private function calculateDailyBalance($bankAccountId, $date)
    {
        $transactions = DB::table('bank_transactions')
            ->where('bank_account_id', $bankAccountId)
            ->where('transaction_date', '<=', $date)
            ->get();
            
        $balance = 0;
        foreach ($transactions as $t) {
            if ($t->type === 'in') {
                $balance += $t->amount;
            } else {
                $balance -= $t->amount;
            }
        }
        
        DB::table('daily_bank_balances')->updateOrInsert(
            [
                'bank_account_id' => $bankAccountId,
                'tgl' => $date
            ],
            [
                'balance' => $balance,
                'exchange_rate' => 1,
                'updated_at' => now()
            ]
        );
    }
}
