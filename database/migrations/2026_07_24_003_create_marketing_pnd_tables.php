<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MARKETING: Kontrak Penjualan ─────────────────────────────────────

        Schema::create('kontrak_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('buyers')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique();
            $table->enum('jenis', ['lokal', 'ekspor'])->default('ekspor');
            $table->enum('mata_uang', ['IDR', 'USD'])->default('USD');
            $table->decimal('qty', 15, 2)->comment('Total qty kontrak (MT)');
            $table->decimal('harga_satuan', 15, 2)->comment('Harga per MT');
            $table->decimal('kurs_konversi', 15, 2)->nullable()->comment('Nilai tukar saat input (dari KMK)');
            $table->string('incoterm')->nullable()->comment('FOB, CIF, CFR, LOCO, FRANCO, dll');
            $table->decimal('levy_rate_usd', 10, 4)->nullable()->comment('Levy duty rate dalam USD/MT');
            $table->string('termin_pembayaran')->nullable()->comment('CAD, CBD, Net30');
            $table->enum('metode_invoice', ['invoice', 'kontrak'])->default('invoice')->comment('Pembayaran via invoice atau langsung kontrak');
            $table->date('tgl_kontrak')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'batal'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── PnD/SCM: Jadwal Kapal ────────────────────────────────────────────

        Schema::create('jadwal_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('nomor_voyage')->nullable();
            $table->string('bendera')->nullable()->comment('Negara kapal');
            $table->date('laycan_start')->nullable()->comment('Earliest Laycan');
            $table->date('laycan_end')->nullable()->comment('Latest Laycan');
            $table->date('eta')->nullable()->comment('Estimated Time of Arrival');
            $table->date('etb')->nullable()->comment('Estimated Time of Berthing');
            $table->date('etd')->nullable()->comment('Estimated Time of Departure');
            $table->string('port_muat')->nullable();
            $table->string('port_bongkar')->nullable();
            $table->enum('status', ['scheduled', 'loading', 'departed', 'arrived', 'done'])->default('scheduled');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── PnD/SCM: Pengiriman Penjualan (Sales Shipment) ──────────────────

        Schema::create('pengiriman_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_penjualan_id')->constrained('kontrak_penjualans')->onDelete('cascade');
            $table->foreignId('jadwal_kapal_id')->nullable()->constrained('jadwal_kapals')->nullOnDelete();
            $table->string('nomor_do')->nullable()->comment('Delivery Order number');
            $table->decimal('qty_order', 15, 2)->comment('Qty dari kontrak yang direncanakan');
            $table->decimal('qty_alokasi', 15, 2)->nullable()->comment('Qty dialokasikan dari stok');
            $table->decimal('qty_kirim', 15, 2)->nullable()->comment('Realisasi qty kirim');
            $table->decimal('qty_terima', 15, 2)->nullable()->comment('Qty diterima di destinasi');
            $table->date('tgl_rencana')->nullable()->comment('Rencana tanggal pengiriman');
            $table->date('tgl_realisasi')->nullable()->comment('Actual tanggal kirim');
            $table->date('laycan_start')->nullable();
            $table->date('laycan_end')->nullable();
            $table->enum('via', ['kapal', 'truk', 'lokal'])->default('kapal');
            $table->enum('status', ['draft', 'allocated', 'scheduled', 'loading', 'shipped', 'done', 'cancelled'])->default('draft');
            $table->string('termin')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── PnD/SCM: Alokasi Stok untuk Pengiriman ──────────────────────────

        Schema::create('alokasi_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_penjualans')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->decimal('qty_alokasi', 15, 2);
            $table->date('tgl_alokasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_stoks');
        Schema::dropIfExists('pengiriman_penjualans');
        Schema::dropIfExists('jadwal_kapals');
        Schema::dropIfExists('kontrak_penjualans');
    }
};
