<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengirimanPenjualansTable extends Migration
{
    public function up()
    {
        Schema::create('pengiriman_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_penjualan_id')->constrained('kontrak_penjualans')->onDelete('cascade');
            $table->decimal('qty_kirim', 15, 2);
            $table->decimal('qty_terima', 15, 2)->nullable();
            $table->string('via')->nullable();
            $table->string('termin')->nullable();
            $table->string('status')->default('pending');
            $table->string('incoterm')->nullable();
            $table->date('tgl');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengiriman_penjualans');
    }
}
