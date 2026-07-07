<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFinanceSchemaForMutasiAndPartialPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create bank_transactions
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 20, 2);
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable(); // e.g. 'PaymentHistory'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        // 2. Modify payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['proses', 'selesai'])->default('proses')->after('job_object');
            // We just allow them to remain as is, we won't strictly drop or change them to nullable using change() since it requires dbal. 
            // The data is basically empty now anyway.
        });

        // 3. Create payment_histories
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->decimal('amount', 20, 2);
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_histories');
        
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::dropIfExists('bank_transactions');
    }
}
