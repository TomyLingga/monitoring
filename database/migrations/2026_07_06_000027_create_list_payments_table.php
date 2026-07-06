<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('list_payments', function (Blueprint $table) {
            $table->id();
            $table->string('mitra');
            $table->decimal('jumlah', 15, 2);
            $table->string('nomor_fr')->nullable();
            $table->string('bank')->nullable();
            $table->string('objek_pekerjaan')->nullable();
            $table->date('tgl_pembayaran_realisasi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('list_payments');
    }
}
