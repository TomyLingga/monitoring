<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TruckingController;
use App\Http\Controllers\Api\LogisticPeTargetController;
use App\Http\Controllers\Api\LogisticAdditionalPeController;
use App\Http\Controllers\Api\LogisticPeUsageController;
use App\Http\Controllers\Api\BuyerController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\Api\MasterProdukController;
use App\Http\Controllers\Api\KontrakCpoController;
use App\Http\Controllers\Api\IncomingCpoController;
use App\Http\Controllers\Api\PembayaranCpoController;
use App\Http\Controllers\Api\KontrakPenjualanController;
use App\Http\Controllers\Api\DailyTargetController;
use App\Http\Controllers\Api\ProsesRefineryController;
use App\Http\Controllers\Api\ProsesFraksinasiController;
use App\Http\Controllers\Api\ProsesPackagingController;
use App\Http\Controllers\Api\DailyProductionTargetController;
use App\Http\Controllers\Api\StokProdukController;
use App\Http\Controllers\Api\PengirimanPenjualanController;
use App\Http\Controllers\Api\PembayaranPenjualanController;
use App\Http\Controllers\Api\DailySalesTargetController;
use App\Http\Controllers\LevyDutyController;

/*
|--------------------------------------------------------------------------
| API Routes — CPO Supply Chain
|--------------------------------------------------------------------------
*/

// ── Auth (public) ──
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// BI Kurs Routes
Route::get('/kurs-bi/uka', [\App\Http\Controllers\KursBIController::class, 'uka']);
Route::get('/kurs-bi/jisdor', [\App\Http\Controllers\KursBIController::class, 'jisdor']);

// Routes with Sanctum middleware
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Master Data
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('buyers', BuyerController::class);
    Route::apiResource('storages', StorageController::class);
    Route::apiResource('master-produks', MasterProdukController::class);

    // Kontrak CPO + Outstanding
    Route::get('kontrak-cpos/outstanding', [KontrakCpoController::class, 'outstandingSummary']);
    Route::apiResource('kontrak-cpos', KontrakCpoController::class);

    // Incoming & Pembayaran CPO
    Route::apiResource('incoming-cpos', IncomingCpoController::class);
    Route::apiResource('pembayaran-cpos', PembayaranCpoController::class);
    Route::apiResource('daily-targets', DailyTargetController::class);

    // Penjualan
    Route::apiResource('kontrak-penjualans', KontrakPenjualanController::class);
    Route::apiResource('pengiriman-penjualans', PengirimanPenjualanController::class);
    Route::apiResource('pembayaran-penjualans', PembayaranPenjualanController::class);
    Route::apiResource('daily-sales-targets', DailySalesTargetController::class);

    // Pembayaran Levy Duty
    Route::post('/levy-duties/bulk', [LevyDutyController::class, 'bulkStore']);
    Route::apiResource('levy-duties', LevyDutyController::class);

    // Bulk Imports
    Route::post('suppliers/bulk', [SupplierController::class, 'bulkStore']);
    Route::post('buyers/bulk', [BuyerController::class, 'bulkStore']);
    Route::post('storages/bulk', [StorageController::class, 'bulkStore']);
    Route::post('master-produks/bulk', [MasterProdukController::class, 'bulkStore']);
    Route::post('incoming-cpos/bulk', [IncomingCpoController::class, 'bulkStore']);
    Route::post('pengiriman-penjualans/bulk', [PengirimanPenjualanController::class, 'bulkStore']);
    Route::post('pembayaran-penjualans/bulk', [PembayaranPenjualanController::class, 'bulkStore']);
    Route::post('proses-refineries/bulk', [ProsesRefineryController::class, 'bulkStore']);
    Route::post('proses-fraksinasis/bulk', [ProsesFraksinasiController::class, 'bulkStore']);
    Route::post('proses-packagings/bulk', [ProsesPackagingController::class, 'bulkStore']);
    Route::post('stok-produks/bulk', [StokProdukController::class, 'bulkStore']);

    // ── Produksi ──
    Route::apiResource('proses-refineries', ProsesRefineryController::class);
    Route::apiResource('proses-fraksinasis', ProsesFraksinasiController::class);
    Route::apiResource('proses-packagings', ProsesPackagingController::class);
    Route::apiResource('daily-production-targets', DailyProductionTargetController::class);
    Route::apiResource('stok-produks', StokProdukController::class);

    // ── Logistik ──
    Route::post('truckings/bulk', [TruckingController::class, 'bulkStore']);
    Route::post('logistic-pe-targets/bulk', [LogisticPeTargetController::class, 'bulkStore']);
    Route::post('logistic-additional-pes/bulk', [LogisticAdditionalPeController::class, 'bulkStore']);
    Route::post('logistic-pe-usages/bulk', [LogisticPeUsageController::class, 'bulkStore']);
    
    Route::get('truckings/summary', [TruckingController::class, 'summary']);
    Route::apiResource('truckings', TruckingController::class);
    Route::apiResource('logistic-pe-targets', LogisticPeTargetController::class);
    Route::apiResource('logistic-additional-pes', LogisticAdditionalPeController::class);
    Route::apiResource('logistic-pe-usages', LogisticPeUsageController::class);

    // ── Keuangan ──
    Route::get('finance/bank-accounts', [\App\Http\Controllers\Api\FinanceController::class, 'getBankAccounts']);
    Route::post('finance/bank-accounts', [\App\Http\Controllers\Api\FinanceController::class, 'storeBankAccount']);
    Route::put('finance/bank-accounts/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'updateBankAccount']);
    Route::delete('finance/bank-accounts/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'destroyBankAccount']);

    Route::get('finance/balances', [\App\Http\Controllers\Api\FinanceController::class, 'getBalances']);

    Route::get('finance/bank-transactions', [\App\Http\Controllers\Api\FinanceController::class, 'getBankTransactions']);
    Route::post('finance/bank-transactions', [\App\Http\Controllers\Api\FinanceController::class, 'storeBankTransaction']);
    Route::put('finance/bank-transactions/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'updateBankTransaction']);
    Route::delete('finance/bank-transactions/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'destroyBankTransaction']);

    Route::get('finance/payments', [\App\Http\Controllers\Api\FinanceController::class, 'getPayments']);
    Route::post('finance/payments', [\App\Http\Controllers\Api\FinanceController::class, 'storePayment']);
    Route::put('finance/payments/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'updatePayment']);
    Route::delete('finance/payments/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'destroyPayment']);

    Route::post('finance/payment-histories', [\App\Http\Controllers\Api\FinanceController::class, 'storePaymentHistory']);
    Route::delete('finance/payment-histories/{id}', [\App\Http\Controllers\Api\FinanceController::class, 'destroyPaymentHistory']);

    Route::get('finance/kurs', [\App\Http\Controllers\Api\FinanceController::class, 'getKurs']);
});
