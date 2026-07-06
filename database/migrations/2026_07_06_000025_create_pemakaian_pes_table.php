<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemakaianPesTable extends Migration
{
    public function up()
    {
        Schema::create('pemakaian_pes', function (Blueprint $table) {
            $table->id();
            $table->decimal('qty', 15, 2);
            $table->date('tgl');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemakaian_pes');
    }
}
