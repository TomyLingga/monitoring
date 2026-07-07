<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStorageIdToProductionDetailsTables extends Migration
{
    public function up()
    {
        $tables = [
            'bahan_refineries',
            'hasil_refineries',
            'bahan_fraksinasis',
            'hasil_fraksinasis',
            'bahan_packagings',
            'hasil_packagings'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // We make it nullable first to prevent errors if there's any existing data, 
                // but since we just migrate:fresh it will be clean.
                $table->foreignId('storage_id')->nullable()->constrained('storages')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        $tables = [
            'bahan_refineries',
            'hasil_refineries',
            'bahan_fraksinasis',
            'hasil_fraksinasis',
            'bahan_packagings',
            'hasil_packagings'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([$tableName . '_storage_id_foreign']);
                $table->dropColumn('storage_id');
            });
        }
    }
}
