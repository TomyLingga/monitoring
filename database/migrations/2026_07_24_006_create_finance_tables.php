<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── KEUANGAN: Rekening Bank ──────────────────────────────────────────

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nama_rekening');
            $table->string('nomor_rekening')->unique();
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Mutasi Bank ────────────────────────────────────────────

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->date('tgl_transaksi');
            $table->enum('tipe', ['debit', 'kredit'])->comment('debit=keluar, kredit=masuk');
            $table->decimal('nominal', 15, 2);
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->decimal('kurs', 15, 2)->nullable();
            $table->string('kategori')->nullable()->comment('CPO, levy_duty, sales, lainnya');
            $table->string('referensi')->nullable()->comment('Nomor kontrak/invoice yang dirujuk');
            // Referensi opsional ke transaksi terkait
            $table->nullableMorphs('transactable');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Pembayaran CPO ─────────────────────────────────────────

        Schema::create('pembayaran_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_cpo_id')->constrained('kontrak_cpos')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->date('tgl_bayar');
            $table->decimal('nominal', 15, 2);
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->decimal('kurs', 15, 2)->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Pembayaran Sales ───────────────────────────────────────

        Schema::create('pembayaran_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_penjualan_id')->nullable()->constrained('kontrak_penjualans')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->date('tgl_bayar');
            $table->decimal('nominal', 15, 2);
            $table->enum('mata_uang', ['IDR', 'USD'])->default('USD');
            $table->decimal('kurs', 15, 2)->nullable()->comment('Kurs jika USD');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Pembayaran Levy Duty ──────────────────────────────────

        Schema::create('pembayaran_levy_duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->date('tgl_bayar');
            $table->decimal('nominal_usd', 15, 2)->comment('Nominal dalam USD');
            $table->decimal('kurs', 15, 2)->nullable()->comment('Kurs IDR/USD saat bayar');
            $table->decimal('nominal_idr', 15, 2)->nullable()->comment('Ekuivalen IDR');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Pembayaran Lainnya ─────────────────────────────────────

        Schema::create('pembayaran_lainnya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->date('tgl_bayar');
            $table->enum('tipe', ['keluar', 'masuk'])->default('keluar');
            $table->decimal('nominal', 15, 2);
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->decimal('kurs', 15, 2)->nullable();
            $table->string('kategori')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── KEUANGAN: Cache Kurs KMK (Kemenkeu) ─────────────────────────────

        Schema::create('kurs_pajak_caches', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique()->comment('Tanggal berlaku kurs');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->json('raw_json')->comment('Seluruh data kurs dalam JSON');
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurs_pajak_caches');
        Schema::dropIfExists('pembayaran_lainnya');
        Schema::dropIfExists('pembayaran_levy_duties');
        Schema::dropIfExists('pembayaran_penjualans');
        Schema::dropIfExists('pembayaran_cpos');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_accounts');
    }
};
