<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePembayaranPenjualanForInvoice extends Migration
{
    public function up()
    {
        // Add invoice_id and bank_account_id to pembayaran_penjualans
        Schema::table('pembayaran_penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('kontrak_penjualan_id');
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('invoice_id');
        });

        // Add bank_account_id to pembayaran_cpos
        Schema::table('pembayaran_cpos', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('kontrak_cpo_id');
        });

        // Add bank_account_id to levy_duties
        Schema::table('levy_duties', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('invoice_id');
        });
    }

    public function down()
    {
        Schema::table('pembayaran_penjualans', function (Blueprint $table) {
            $table->dropColumn(['invoice_id', 'bank_account_id']);
        });

        Schema::table('pembayaran_cpos', function (Blueprint $table) {
            $table->dropColumn('bank_account_id');
        });

        Schema::table('levy_duties', function (Blueprint $table) {
            $table->dropColumn('bank_account_id');
        });
    }
}
