<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranCposTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_cpo_id')->constrained('kontrak_cpos')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->date('tgl_bayar');
            $table->string('metode_bayar')->nullable()->comment('transfer/tunai/giro');
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_cpos');
    }
}
