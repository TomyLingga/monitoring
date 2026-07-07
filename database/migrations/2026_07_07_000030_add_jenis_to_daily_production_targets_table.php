<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisToDailyProductionTargetsTable extends Migration
{
    public function up()
    {
        Schema::table('daily_production_targets', function (Blueprint $table) {
            // Add process type column
            $table->string('jenis')->default('refinery')->after('tgl');

            // Drop old unique on tgl alone, add composite unique (tgl, jenis)
            $table->dropUnique(['tgl']);
            $table->unique(['tgl', 'jenis']);
        });
    }

    public function down()
    {
        Schema::table('daily_production_targets', function (Blueprint $table) {
            $table->dropUnique(['tgl', 'jenis']);
            $table->dropColumn('jenis');
            $table->unique(['tgl']);
        });
    }
}
