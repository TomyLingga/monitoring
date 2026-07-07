<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDashboardTables extends Migration
{
    public function up()
    {
        // 1. Add storage_id to pengiriman_penjualans
        Schema::table('pengiriman_penjualans', function (Blueprint $table) {
            $table->foreignId('storage_id')->nullable()->constrained('storages')->onDelete('set null');
        });

        // 2. Create pembayaran_penjualans
        Schema::create('pembayaran_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_penjualan_id')->constrained('kontrak_penjualans')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->date('tgl_bayar');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_penjualans');

        Schema::table('pengiriman_penjualans', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
            $table->dropColumn('storage_id');
        });
    }
}
