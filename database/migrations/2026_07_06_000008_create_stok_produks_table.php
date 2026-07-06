<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokProduksTable extends Migration
{
    public function up()
    {
        Schema::create('stok_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('master_produks')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->decimal('qty', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_produks');
    }
}
