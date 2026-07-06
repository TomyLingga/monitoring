<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontrakPenjualansTable extends Migration
{
    public function up()
    {
        Schema::create('kontrak_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('buyers')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique();
            $table->decimal('qty', 15, 2);
            $table->decimal('harga_satuan', 15, 2);
            $table->date('tgl_kontrak')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->string('termin_pembayaran')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'batal'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kontrak_penjualans');
    }
}
