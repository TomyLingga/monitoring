<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyProductionTargetsTable extends Migration
{
    public function up()
    {
        Schema::create('daily_production_targets', function (Blueprint $table) {
            $table->id();
            $table->date('tgl')->unique();
            $table->decimal('target_qty', 15, 2)->default(0);
            $table->string('satuan')->default('Kg');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_production_targets');
    }
}
