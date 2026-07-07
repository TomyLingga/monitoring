<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SupplierController;
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

/*
|--------------------------------------------------------------------------
| API Routes — CPO Supply Chain
|--------------------------------------------------------------------------
*/

// ── Auth (public) ──
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ── Protected routes ──
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

    // ── Produksi ──
    Route::apiResource('proses-refineries', ProsesRefineryController::class);
    Route::apiResource('proses-fraksinasis', ProsesFraksinasiController::class);
    Route::apiResource('proses-packagings', ProsesPackagingController::class);
    Route::apiResource('daily-production-targets', DailyProductionTargetController::class);
    Route::apiResource('stok-produks', StokProdukController::class);
});
