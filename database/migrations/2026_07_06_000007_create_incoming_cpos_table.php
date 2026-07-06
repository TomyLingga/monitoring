<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingCposTable extends Migration
{
    public function up()
    {
        Schema::create('incoming_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_cpo_id')->constrained('kontrak_cpos')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->decimal('qty', 15, 2);
            $table->date('tgl');
            $table->string('no_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_kendaraan')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incoming_cpos');
    }
}
