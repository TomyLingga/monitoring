<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProduksTable extends Migration
{
    public function up()
    {
        Schema::create('master_produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('satuan')->default('Kg');
            $table->string('kode_produk')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_produks');
    }
}
