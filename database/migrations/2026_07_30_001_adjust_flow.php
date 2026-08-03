<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom kebutuhan CPO di pengiriman_penjualans
        Schema::table('pengiriman_penjualans', function (Blueprint $table) {
            $table->decimal('kebutuhan_cpo_kg', 15, 2)->nullable()->after('qty_terima')
                  ->comment('Kebutuhan CPO (Kg) untuk memenuhi DO ini');
            $table->decimal('kebutuhan_cpo_terpenuhi', 15, 2)->default(0)->after('kebutuhan_cpo_kg')
                  ->comment('CPO yg sudah dipenuhi sourcing (Kg)');
        });

        // 2. Tabel kebutuhan CPO per DO (request dari PnD ke Sourcing)
        Schema::create('kebutuhan_cpo_dos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_penjualans')->onDelete('cascade');
            $table->decimal('qty_butuh_kg', 15, 2)->comment('Kebutuhan CPO dalam Kg');
            $table->decimal('qty_terpenuhi_kg', 15, 2)->default(0)->comment('Sudah dipenuhi incoming CPO');
            $table->date('deadline')->nullable()->comment('Deadline pemenuhan');
            $table->enum('status', ['pending', 'partial', 'done'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Tambah referensi kebutuhan_cpo_do_id di incoming_cpos
        Schema::table('incoming_cpos', function (Blueprint $table) {
            $table->unsignedBigInteger('kebutuhan_cpo_do_id')->nullable()->after('kontrak_cpo_id');
        });

        // 4. Tambah kolom dpp, ppn, total di invoices jika belum ada
        if (!Schema::hasColumn('invoices', 'dpp')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('dpp', 15, 2)->nullable()->after('nilai_invoice');
                $table->decimal('ppn', 15, 2)->nullable()->after('dpp');
                $table->decimal('total', 15, 2)->nullable()->after('ppn');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_cpo_dos');

        Schema::table('pengiriman_penjualans', function (Blueprint $table) {
            $table->dropColumn(['kebutuhan_cpo_kg', 'kebutuhan_cpo_terpenuhi']);
        });

        Schema::table('incoming_cpos', function (Blueprint $table) {
            $table->dropColumn('kebutuhan_cpo_do_id');
        });

        if (Schema::hasColumn('invoices', 'dpp')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn(['dpp', 'ppn', 'total']);
            });
        }
    }
};
