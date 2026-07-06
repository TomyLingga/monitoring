<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProsesPackagingsTable extends Migration
{
    public function up()
    {
        Schema::create('proses_packagings', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->string('shift')->nullable();
            $table->string('operator')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proses_packagings');
    }
}
