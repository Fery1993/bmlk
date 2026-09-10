<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CoaController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Transaksi\InvoiceController;
use App\Http\Controllers\Transaksi\BillController;
use App\Http\Controllers\Transaksi\FeeController;
use App\Http\Controllers\Transaksi\JournalController;
use App\Http\Controllers\Laporan\LaporanController;

// ── Auth ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── App (semua butuh login) ───────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('coa',       CoaController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('vendors',   VendorController::class);
        Route::resource('karyawan',  KaryawanController::class);
        Route::resource('users',     UserController::class);
    });

    // Transaksi
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::resource('bills',    BillController::class);
        Route::resource('fee',      FeeController::class);
        Route::resource('journals', JournalController::class);

        // Penerimaan & pembayaran
        Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'payment'])
             ->name('invoices.payment');
        Route::get('invoices/{invoice}/cetak', [InvoiceController::class, 'cetak'])
             ->name('invoices.cetak');
        Route::post('bills/{bill}/payment', [BillController::class, 'payment'])
             ->name('bills.payment');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('piutang',      [LaporanController::class, 'piutang'])->name('piutang');
        Route::get('hutang',       [LaporanController::class, 'hutang'])->name('hutang');
        Route::get('gl',           [LaporanController::class, 'generalLedger'])->name('gl');
        Route::get('neraca-saldo', [LaporanController::class, 'neracaSaldo'])->name('neraca-saldo');
        Route::get('neraca',       [LaporanController::class, 'neraca'])->name('neraca');
        Route::get('laba-rugi',    [LaporanController::class, 'labaRugi'])->name('laba-rugi');
    });

});
