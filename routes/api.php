<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
// Master Data
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\{SupplierController, BuyerController, StorageController, MasterProdukController};
// Sourcing
use App\Http\Controllers\Api\SourcingController;
use App\Http\Controllers\Api\{KontrakCpoController, IncomingCpoController, StokProdukController};
// Marketing + PnD
use App\Http\Controllers\Api\MarketingPnDController;
use App\Http\Controllers\Api\{KontrakPenjualanController, JadwalKapalController, PengirimanPenjualanController, AlokasiStokController, InvoiceController};
// Produksi
use App\Http\Controllers\Api\ProduksiController;
use App\Http\Controllers\Api\{KebutuhanProduksiController, ProsesRefineryController, ProsesFraksinasiController, ProsesPackagingController};
// Logistik
use App\Http\Controllers\Api\LogistikController;
use App\Http\Controllers\Api\{TruckingController, KebutuhanPeController, PenguranganPeController};
use App\Http\Controllers\Api\KebutuhanCpoDoController;
// Finance
use App\Http\Controllers\Api\FinanceController;
// Kurs
use App\Http\Controllers\Api\KursPajakController;

/*
|--------------------------------------------------------------------------
| API Routes — CPO Planning & Distribution Dashboard
|--------------------------------------------------------------------------
*/

// ── Auth (public) ──
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ── Kurs (public) ──
Route::get('/kurs-bi/uka',    [\App\Http\Controllers\KursBIController::class, 'uka']);
Route::get('/kurs-bi/jisdor', [\App\Http\Controllers\KursBIController::class, 'jisdor']);
Route::get('/kurs-pajak',         [KursPajakController::class, 'index']);
Route::get('/kurs-pajak/history', [KursPajakController::class, 'history']);

// ── Protected ──
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ─── Master Data ───────────────────────────────────────────────────────────
    Route::apiResource('suppliers',    SupplierController::class);
    Route::apiResource('buyers',       BuyerController::class);
    Route::apiResource('storages',     StorageController::class);
    Route::apiResource('master-produks', MasterProdukController::class);

    // ─── Sourcing ──────────────────────────────────────────────────────────────
    Route::apiResource('kontrak-cpos',  KontrakCpoController::class);
    Route::apiResource('incoming-cpos', IncomingCpoController::class);
    Route::apiResource('stok-produks',  StokProdukController::class);

    // ─── Marketing ─────────────────────────────────────────────────────────────
    Route::apiResource('kontrak-penjualans', KontrakPenjualanController::class);
    Route::apiResource('invoices',           InvoiceController::class);

    // ─── PnD/SCM ───────────────────────────────────────────────────────────────
    Route::apiResource('jadwal-kapals',           JadwalKapalController::class);
    Route::apiResource('pengiriman-penjualans',   PengirimanPenjualanController::class);
    Route::apiResource('alokasi-stoks',           AlokasiStokController::class);
    Route::apiResource('kebutuhan-cpo-dos',       KebutuhanCpoDoController::class);
    Route::post('kebutuhan-cpo-dos/{id}/fulfill', [KebutuhanCpoDoController::class, 'fulfill']);

    // ─── Produksi ──────────────────────────────────────────────────────────────
    Route::apiResource('kebutuhan-produksis',  KebutuhanProduksiController::class);
    Route::apiResource('proses-refineries',    ProsesRefineryController::class);
    Route::apiResource('proses-fraksinasis',   ProsesFraksinasiController::class);
    Route::apiResource('proses-packagings',    ProsesPackagingController::class);

    // ─── Logistik ──────────────────────────────────────────────────────────────
    Route::apiResource('truckings',       TruckingController::class);
    Route::apiResource('kebutuhan-pes',   KebutuhanPeController::class);
    Route::apiResource('pengurangan-pes', PenguranganPeController::class)->only(['store','destroy']);

    // ─── Keuangan ──────────────────────────────────────────────────────────────
    Route::prefix('finance')->group(function () {
        // Rekening Bank
        Route::get('bank-accounts',         [FinanceController::class, 'getBankAccounts']);
        Route::post('bank-accounts',        [FinanceController::class, 'storeBankAccount']);
        Route::put('bank-accounts/{id}',    [FinanceController::class, 'updateBankAccount']);
        Route::delete('bank-accounts/{id}', [FinanceController::class, 'destroyBankAccount']);

        // Saldo
        Route::get('balances', [FinanceController::class, 'getBalances']);

        // Mutasi / Transaksi
        Route::get('bank-transactions',         [FinanceController::class, 'getBankTransactions']);
        Route::post('bank-transactions',        [FinanceController::class, 'storeBankTransaction']);
        Route::put('bank-transactions/{id}',    [FinanceController::class, 'updateBankTransaction']);
        Route::delete('bank-transactions/{id}', [FinanceController::class, 'destroyBankTransaction']);

        // Pembayaran CPO
        Route::get('pembayaran-cpos',         [FinanceController::class, 'getPembayaranCpos']);
        Route::post('pembayaran-cpos',        [FinanceController::class, 'storePembayaranCpo']);
        Route::delete('pembayaran-cpos/{id}', [FinanceController::class, 'destroyPembayaranCpo']);

        // Pembayaran Sales
        Route::get('pembayaran-penjualans',         [FinanceController::class, 'getPembayaranPenjualans']);
        Route::post('pembayaran-penjualans',        [FinanceController::class, 'storePembayaranPenjualan']);
        Route::delete('pembayaran-penjualans/{id}', [FinanceController::class, 'destroyPembayaranPenjualan']);

        // Pembayaran Levy Duty
        Route::get('pembayaran-levy-duties',         [FinanceController::class, 'getPembayaranLevyDuties']);
        Route::post('pembayaran-levy-duties',        [FinanceController::class, 'storePembayaranLevyDuty']);
        Route::delete('pembayaran-levy-duties/{id}', [FinanceController::class, 'destroyPembayaranLevyDuty']);

        // Pembayaran Lainnya
        Route::get('pembayaran-lainnya',         [FinanceController::class, 'getPembayaranLainnya']);
        Route::post('pembayaran-lainnya',        [FinanceController::class, 'storePembayaranLainnya']);
        Route::delete('pembayaran-lainnya/{id}', [FinanceController::class, 'destroyPembayaranLainnya']);
    });
});
