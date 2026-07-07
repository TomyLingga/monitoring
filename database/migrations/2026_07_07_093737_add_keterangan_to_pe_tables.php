<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToPeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('logistic_additional_pes', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
        });
        Schema::table('logistic_pe_usages', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('logistic_additional_pes', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        Schema::table('logistic_pe_usages', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}
