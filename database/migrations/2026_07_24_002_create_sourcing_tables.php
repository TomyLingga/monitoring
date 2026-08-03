<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── SOURCING: Kontrak CPO ────────────────────────────────────────────

        Schema::create('kontrak_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique();
            $table->enum('jenis', ['lokal', 'impor'])->default('lokal');
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->decimal('qty', 15, 2)->comment('Total qty kontrak (MT)');
            $table->decimal('harga_per_kg', 15, 2)->comment('Harga per MT/Kg');
            $table->string('termin_pembayaran')->nullable()->comment('CAD, CBD, Net30');
            $table->date('tgl_kontrak')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'batal'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── SOURCING: Penerimaan CPO ─────────────────────────────────────────

        Schema::create('incoming_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_cpo_id')->nullable()->constrained('kontrak_cpos')->nullOnDelete();
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->string('nomor_do')->nullable();
            $table->decimal('qty_order', 15, 2)->nullable()->comment('Qty yang dipesan');
            $table->decimal('qty_terima', 15, 2)->comment('Qty aktual diterima');
            $table->decimal('harga_per_kg', 15, 2)->nullable()->comment('Override harga jika berbeda dari kontrak');
            $table->date('tgl_rencana')->nullable()->comment('ETA rencana');
            $table->date('tgl_terima')->comment('Tanggal realisasi terima');
            $table->string('no_dok')->nullable()->comment('No. surat jalan / BL');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── STOK PRODUK ──────────────────────────────────────────────────────

        Schema::create('stok_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->decimal('qty', 15, 2)->default(0)->comment('Stok saat ini');
            $table->decimal('harga_satuan', 15, 2)->nullable()->comment('Harga satuan per MT');
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR');
            $table->date('tgl_update')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_produks');
        Schema::dropIfExists('incoming_cpos');
        Schema::dropIfExists('kontrak_cpos');
    }
};
