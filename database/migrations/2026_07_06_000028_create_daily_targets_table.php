<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyTargetsTable extends Migration
{
    public function up()
    {
        Schema::create('daily_targets', function (Blueprint $table) {
            $table->id();
            $table->date('tgl')->unique();
            $table->decimal('target_qty', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_targets');
    }
}
