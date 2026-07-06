<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontrakCposTable extends Migration
{
    public function up()
    {
        Schema::create('kontrak_cpos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique();
            $table->decimal('qty', 15, 2);
            $table->decimal('harga_per_kg', 15, 2);
            $table->string('cbd_cad')->nullable()->comment('Cash Before Delivery / Cash Against Delivery');
            $table->date('tgl_kontrak')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'batal'])->default('aktif');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kontrak_cpos');
    }
}
