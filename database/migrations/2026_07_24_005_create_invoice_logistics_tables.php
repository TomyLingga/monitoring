<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── MARKETING: Invoice ───────────────────────────────────────────────

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_penjualan_id')->constrained('kontrak_penjualans')->onDelete('cascade');
            $table->foreignId('pengiriman_id')->nullable()->constrained('pengiriman_penjualans')->nullOnDelete();
            $table->string('nomor_invoice')->unique();
            $table->decimal('qty', 15, 2)->comment('Qty yang diinvoice (MT)');
            $table->decimal('harga_satuan', 15, 2);
            $table->enum('mata_uang', ['IDR', 'USD'])->default('USD');
            $table->decimal('kurs_konversi', 15, 2)->nullable();
            $table->decimal('nilai_invoice', 15, 2)->comment('Total nilai invoice');
            $table->decimal('levy_amount', 15, 2)->nullable()->comment('Total levy duty (USD)');
            $table->decimal('levy_kurs', 15, 2)->nullable()->comment('Kurs saat penghitungan levy');
            $table->date('tgl_invoice');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('status', ['draft', 'terbit', 'lunas', 'batal'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── LOGISTIK: Trucking ───────────────────────────────────────────────

        Schema::create('truckings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->nullable()->constrained('pengiriman_penjualans')->nullOnDelete();
            $table->date('tgl');
            $table->string('transporter_name')->comment('Nama transporter/trucking');
            $table->string('destination')->nullable()->comment('Tujuan pengiriman');
            $table->integer('qty_unit')->default(1)->comment('Jumlah unit kendaraan');
            $table->decimal('qty_produk', 15, 2)->comment('Qty produk (MT)');
            $table->foreignId('produk_id')->nullable()->constrained('master_produks')->nullOnDelete();
            $table->string('no_polisi')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── LOGISTIK: Kebutuhan PE (Palm Effluent) ───────────────────────────
        // Satuan: Kg — "Target PE" diganti "Kebutuhan PE"

        Schema::create('kebutuhan_pes', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->string('periode')->nullable()->comment('Bulan/minggu referensi');
            $table->decimal('qty_kebutuhan', 15, 2)->comment('Kebutuhan PE (Kg)');
            $table->decimal('qty_realisasi', 15, 2)->default(0)->comment('Realisasi PE (Kg)');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ─── LOGISTIK: Pengurangan PE ─────────────────────────────────────────
        // Pengurangan PE otomatis mengurangi kebutuhan

        Schema::create('pengurangan_pes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kebutuhan_pe_id')->nullable()->constrained('kebutuhan_pes')->nullOnDelete();
            $table->date('tgl');
            $table->decimal('qty', 15, 2)->comment('Qty pengurangan PE (Kg)');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurangan_pes');
        Schema::dropIfExists('kebutuhan_pes');
        Schema::dropIfExists('truckings');
        Schema::dropIfExists('invoices');
    }
};
