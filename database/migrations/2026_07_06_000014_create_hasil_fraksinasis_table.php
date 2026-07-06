<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilFraksinasisTable extends Migration
{
    public function up()
    {
        Schema::create('hasil_fraksinasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_fraksinasi_id')->constrained('proses_fraksinasis')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hasil_fraksinasis');
    }
}
