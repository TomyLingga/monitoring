<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToBuyersAndSuppliers extends Migration
{
    public function up()
    {
        Schema::table('buyers', function (Blueprint $table) {
            $table->string('keterangan')->default('lokal'); // 'lokal', 'ekspor'
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('keterangan')->default('lokal'); // 'lokal', 'impor'
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}
