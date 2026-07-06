<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBahanPackagingsTable extends Migration
{
    public function up()
    {
        Schema::create('bahan_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proses_packaging_id')->constrained('proses_packagings')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bahan_packagings');
    }
}
