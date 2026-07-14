<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTruckingsTable extends Migration
{
    public function up()
    {
        Schema::table('truckings', function (Blueprint $table) {
            $table->string('destination')->nullable()->after('transporter');
            $table->integer('qty_unit')->default(0)->after('destination');
        });
    }

    public function down()
    {
        Schema::table('truckings', function (Blueprint $table) {
            $table->dropColumn(['destination', 'qty_unit']);
        });
    }
}
