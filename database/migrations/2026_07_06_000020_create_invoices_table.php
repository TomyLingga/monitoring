<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_penjualans')->onDelete('cascade');
            $table->string('nomor_invoice')->unique();
            $table->decimal('nilai', 15, 2);
            $table->date('tgl_invoice')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->enum('status', ['draft', 'terkirim', 'lunas', 'overdue'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
