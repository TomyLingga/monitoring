<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetPeBulanansTable extends Migration
{
    public function up()
    {
        Schema::create('target_pe_bulanans', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah', 15, 2);
            $table->date('tgl');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_pe_bulanans');
    }
}
