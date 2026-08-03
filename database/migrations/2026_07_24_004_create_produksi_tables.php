<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── PRODUKSI: Laporan Proses ─────────────────────────────────────────

        Schema::create('proses_refineries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->date('tgl_proses');
            $table->decimal('total_bahan', 15, 2)->default(0)->comment('Total CPO masuk (MT)');
            $table->decimal('total_hasil', 15, 2)->default(0)->comment('Total output (MT)');
            $table->decimal('losses', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('bahan_refineries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_refinery_id')->constrained('proses_refineries')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        Schema::create('hasil_refineries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_refinery_id')->constrained('proses_refineries')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        Schema::create('proses_fraksinasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->date('tgl_proses');
            $table->decimal('total_bahan', 15, 2)->default(0);
            $table->decimal('total_hasil', 15, 2)->default(0);
            $table->decimal('losses', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('bahan_fraksinasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_fraksinasi_id')->constrained('proses_fraksinasis')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        Schema::create('hasil_fraksinasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_fraksinasi_id')->constrained('proses_fraksinasis')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        Schema::create('proses_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->date('tgl_proses');
            $table->decimal('total_bahan', 15, 2)->default(0);
            $table->decimal('total_hasil', 15, 2)->default(0);
            $table->decimal('losses', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('bahan_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_packaging_id')->constrained('proses_packagings')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        Schema::create('hasil_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_packaging_id')->constrained('proses_packagings')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->nullOnDelete();
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });

        // ─── PRODUKSI: Kebutuhan Produksi per Kontrak/SO ─────────────────────
        // Menggantikan "target produksi harian" dengan kebutuhan per sales order

        Schema::create('kebutuhan_produksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_penjualans')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->decimal('qty_butuh', 15, 2)->comment('Total kebutuhan produksi (MT)');
            $table->decimal('qty_terproduksi', 15, 2)->default(0)->comment('Sudah diproduksi');
            $table->date('deadline')->nullable()->comment('Deadline cargo ready');
            $table->enum('status', ['pending', 'partial', 'done'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_produksis');
        Schema::dropIfExists('hasil_packagings');
        Schema::dropIfExists('bahan_packagings');
        Schema::dropIfExists('proses_packagings');
        Schema::dropIfExists('hasil_fraksinasis');
        Schema::dropIfExists('bahan_fraksinasis');
        Schema::dropIfExists('proses_fraksinasis');
        Schema::dropIfExists('hasil_refineries');
        Schema::dropIfExists('bahan_refineries');
        Schema::dropIfExists('proses_refineries');
    }
};
