<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LevyDutySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * levy_duties dan pembayaran_levy_duties terhubung ke invoices (via invoice_id).
     * Invoice butuh pengiriman_id, pengiriman butuh kontrak_penjualan_id.
     * Chain: buyer -> produk -> kontrak -> pengiriman -> invoice -> levy_duty -> pembayaran_levy_duty
     */
    public function run(): void
    {
        $today = Carbon::today();

        // --- Pastikan Buyer ada ---
        $buyerId = DB::table('buyers')->first()?->id;
        if (!$buyerId) {
            $buyerId = DB::table('buyers')->insertGetId([
                'nama'       => 'PT Levy Buyer Demo',
                'alamat'     => 'Medan',
                'pic'        => 'Andi',
                'no_hp'      => '08111222333',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- Pastikan Produk ada ---
        $produkId = DB::table('master_produks')->first()?->id;
        if (!$produkId) {
            $produkId = DB::table('master_produks')->insertGetId([
                'nama_produk' => 'CPO',
                'kode_produk' => 'CPO',
                'satuan'      => 'Kg',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // --- 3 data levy duty dengan bulan berbeda ---
        $entries = [
            [
                'label'          => $today->copy()->subMonths(2)->format('Ym'),
                'tgl_kontrak'    => $today->copy()->subMonths(2)->startOfMonth()->format('Y-m-d'),
                'tgl_kirim'      => $today->copy()->subDays(65)->format('Y-m-d'),
                'qty_kirim'      => 15000000, // 15.000 ton = 15.000.000 Kg
                'harga_satuan'   => 900,
                'tarif'          => 200.00,
                'kurs'           => 15000.00,
                'nilai_akhir'    => 200 * 15000 * 15000.00, // tarif_per_ton * qty_ton * kurs
                'kapal'          => 'MV Sriwijaya',
                'status_invoice' => 'lunas',
                'tgl_bayar'      => $today->copy()->subDays(55)->format('Y-m-d'),
                'nominal_bayar'  => 200 * 15000 * 15000.00, // lunas penuh
            ],
            [
                'label'          => $today->copy()->subMonth()->format('Ym'),
                'tgl_kontrak'    => $today->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
                'tgl_kirim'      => $today->copy()->subDays(35)->format('Y-m-d'),
                'qty_kirim'      => 12000000,
                'harga_satuan'   => 920,
                'tarif'          => 200.00,
                'kurs'           => 15200.00,
                'nilai_akhir'    => 200 * 12000 * 15200.00,
                'kapal'          => 'MV Kalimantan',
                'status_invoice' => 'terkirim',
                'tgl_bayar'      => $today->copy()->subDays(20)->format('Y-m-d'),
                'nominal_bayar'  => round(200 * 12000 * 15200.00 * 0.5), // 50% dibayar
            ],
            [
                'label'          => $today->format('Ym'),
                'tgl_kontrak'    => $today->copy()->startOfMonth()->format('Y-m-d'),
                'tgl_kirim'      => $today->copy()->subDays(7)->format('Y-m-d'),
                'qty_kirim'      => 18000000,
                'harga_satuan'   => 950,
                'tarif'          => 200.00,
                'kurs'           => 15400.00,
                'nilai_akhir'    => 200 * 18000 * 15400.00,
                'kapal'          => 'MV Nusantara',
                'status_invoice' => 'terkirim',
                'tgl_bayar'      => $today->copy()->subDays(2)->format('Y-m-d'),
                'nominal_bayar'  => round(200 * 18000 * 15400.00 * 0.25), // 25% dibayar
            ],
        ];

        foreach ($entries as $e) {
            // 1. Kontrak Penjualan
            $kontrakId = DB::table('kontrak_penjualans')->insertGetId([
                'nomor_kontrak'      => 'KPJL-LEVY-' . $e['label'],
                'buyer_id'           => $buyerId,
                'produk_id'          => $produkId,
                'qty'                => $e['qty_kirim'],
                'harga_satuan'       => $e['harga_satuan'],
                'tgl_kontrak'        => $e['tgl_kontrak'],
                'termin_pembayaran'  => 'CAD',
                'status'             => 'selesai',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // 2. Pengiriman Penjualan
            $pengirimanId = DB::table('pengiriman_penjualans')->insertGetId([
                'kontrak_penjualan_id' => $kontrakId,
                'tgl'                  => $e['tgl_kirim'],
                'qty_kirim'            => $e['qty_kirim'],
                'qty_terima'           => $e['qty_kirim'],
                'via'                  => 'Kapal Tanker',
                'incoterm'             => 'FRANCO',
                'termin'               => 'CAD',
                'status'               => 'Selesai',
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // 3. Invoice
            $invoiceId = DB::table('invoices')->insertGetId([
                'pengiriman_id'   => $pengirimanId,
                'nomor_invoice'   => 'INV-LEVY-' . $e['label'] . '-' . rand(100, 999),
                'nilai'           => $e['qty_kirim'] * $e['harga_satuan'],
                'tgl_invoice'     => $e['tgl_kirim'],
                'tgl_jatuh_tempo' => Carbon::parse($e['tgl_kirim'])->addDays(30)->format('Y-m-d'),
                'status'          => $e['status_invoice'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 4. Levy Duty
            DB::table('levy_duties')->insert([
                'invoice_id'  => $invoiceId,
                'kapal'       => $e['kapal'],
                'tarif'       => $e['tarif'],
                'kurs'        => $e['kurs'],
                'nilai_akhir' => $e['nilai_akhir'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 5. Pembayaran Levy Duty
            DB::table('pembayaran_levy_duties')->insert([
                'invoice_id'  => $invoiceId,
                'kapal'       => $e['kapal'],
                'tarif'       => $e['tarif'],
                'kurs'        => $e['kurs'],
                'nilai_akhir' => $e['nominal_bayar'],
                'created_at'  => $e['tgl_bayar'] . ' 09:00:00',
                'updated_at'  => now(),
            ]);
        }
    }
}
