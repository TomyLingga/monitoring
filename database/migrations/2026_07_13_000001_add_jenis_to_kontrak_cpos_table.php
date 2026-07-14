<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisToKontrakCposTable extends Migration
{
    public function up()
    {
        Schema::table('kontrak_cpos', function (Blueprint $table) {
            $table->enum('jenis', ['lokal', 'impor'])->default('lokal')->after('nomor_kontrak');
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR')->after('jenis');
        });
    }

    public function down()
    {
        Schema::table('kontrak_cpos', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'mata_uang']);
        });
    }
}
