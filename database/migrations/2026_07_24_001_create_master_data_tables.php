<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MASTER DATA ─────────────────────────────────────────────────────

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->string('negara')->nullable()->default('Indonesia');
            $table->string('kontak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->enum('tipe', ['tangki', 'gudang', 'silo', 'lainnya'])->default('tangki');
            $table->decimal('kapasitas', 15, 2)->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('master_produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kode_produk')->unique();
            $table->enum('kategori', ['CPO', 'Olein', 'Stearin', 'PFAD', 'RBDPO', 'PKO', 'Lainnya'])->default('Lainnya');
            $table->string('satuan')->default('MT');
            $table->decimal('yield_dari_cpo', 8, 4)->nullable()->comment('Rasio konversi dari CPO, e.g. 0.82 berarti 82%');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_produks');
        Schema::dropIfExists('storages');
        Schema::dropIfExists('buyers');
        Schema::dropIfExists('suppliers');
    }
};
