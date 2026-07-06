<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyersTable extends Migration
{
    public function up()
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('pic')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buyers');
    }
}
