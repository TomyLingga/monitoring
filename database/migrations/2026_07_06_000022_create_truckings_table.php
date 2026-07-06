<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTruckingsTable extends Migration
{
    public function up()
    {
        Schema::create('truckings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_penjualans')->onDelete('cascade');
            $table->string('no_do')->nullable();
            $table->decimal('qty', 15, 2);
            $table->integer('unit_tersedia')->default(0);
            $table->string('transporter')->nullable();
            $table->date('tgl');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('truckings');
    }
}
