<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoragesTable extends Migration
{
    public function up()
    {
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tangki');
            $table->enum('jenis', ['tangki', 'gudang'])->default('tangki');
            $table->decimal('kapasitas', 15, 2)->nullable();
            $table->string('tipe')->nullable()->comment('CPO, RPO, PFAD, Olein, Stearin, dll');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('storages');
    }
}
