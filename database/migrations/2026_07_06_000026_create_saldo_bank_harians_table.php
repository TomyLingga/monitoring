<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoBankHariansTable extends Migration
{
    public function up()
    {
        Schema::create('saldo_bank_harians', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo', 15, 2);
            $table->date('tgl');
            $table->string('no_rek');
            $table->string('nama_bank')->nullable();
            $table->decimal('kurs', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saldo_bank_harians');
    }
}
