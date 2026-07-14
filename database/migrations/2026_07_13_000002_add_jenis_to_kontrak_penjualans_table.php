<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisToKontrakPenjualansTable extends Migration
{
    public function up()
    {
        Schema::table('kontrak_penjualans', function (Blueprint $table) {
            $table->enum('jenis', ['lokal', 'ekspor'])->default('lokal')->after('nomor_kontrak');
            $table->enum('mata_uang', ['IDR', 'USD'])->default('IDR')->after('jenis');
            $table->string('incoterm')->nullable()->after('mata_uang');
        });
    }

    public function down()
    {
        Schema::table('kontrak_penjualans', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'mata_uang', 'incoterm']);
        });
    }
}
