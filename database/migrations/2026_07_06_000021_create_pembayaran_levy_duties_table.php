<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranLevyDutiesTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_levy_duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('kapal')->nullable();
            $table->decimal('tarif', 15, 2);
            $table->decimal('kurs', 15, 2);
            $table->decimal('nilai_akhir', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_levy_duties');
    }
}
