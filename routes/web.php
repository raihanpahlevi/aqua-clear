<?php

use App\Http\Controllers\CycleController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmergencyLogController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\InventoryUsageController;
use App\Http\Controllers\PondController;
use App\Http\Controllers\PondMaintenanceLogController;
use App\Http\Controllers\PrepLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SamplingController;
use App\Http\Controllers\StockingController;
use App\Http\Controllers\WaterQualityWeeklyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Data Kolam — kelola oleh Owner + Operasional, semua role bisa lihat
    // Catatan: rute statis (create) HARUS didaftarkan sebelum rute dinamis ({pond}) agar tidak tertangkap sebagai parameter.
    Route::get('ponds', [PondController::class, 'index'])->name('ponds.index');
    Route::middleware('role:owner,operasional')->group(function () {
        Route::get('ponds/create', [PondController::class, 'create'])->name('ponds.create');
        Route::post('ponds', [PondController::class, 'store'])->name('ponds.store');
        Route::get('ponds/bulk-create', [PondController::class, 'bulkCreate'])->name('ponds.bulk-create');
        Route::post('ponds/bulk-store', [PondController::class, 'bulkStore'])->name('ponds.bulk-store');
    });
    Route::get('ponds/{pond}', [PondController::class, 'show'])->name('ponds.show');
    Route::middleware('role:owner,operasional')->group(function () {
        Route::get('ponds/{pond}/edit', [PondController::class, 'edit'])->name('ponds.edit');
        Route::put('ponds/{pond}', [PondController::class, 'update'])->name('ponds.update');
        Route::delete('ponds/{pond}', [PondController::class, 'destroy'])->name('ponds.destroy');
    });

    // Persiapan Tambak & Air (Pencatatan Dasar) — kelola oleh Owner + Operasional, semua role bisa lihat
    Route::prefix('ponds/{pond}')->name('ponds.')->group(function () {
        Route::get('prep-logs', [PrepLogController::class, 'index'])->name('prep-logs.index');
        Route::middleware('role:owner,operasional')->group(function () {
            Route::get('prep-logs/create', [PrepLogController::class, 'create'])->name('prep-logs.create');
            Route::post('prep-logs', [PrepLogController::class, 'store'])->name('prep-logs.store');
            Route::get('prep-logs/{prepLog}/edit', [PrepLogController::class, 'edit'])->name('prep-logs.edit');
            Route::put('prep-logs/{prepLog}', [PrepLogController::class, 'update'])->name('prep-logs.update');
        });
    });

    // Laporan — ringkasan biaya/pendapatan lintas semua kolam, read-only, semua role bisa lihat
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Siklus (nama batch) — kelola oleh Owner + Operasional, semua role bisa lihat
    Route::get('cycles', [CycleController::class, 'index'])->name('cycles.index');
    Route::middleware('role:owner,operasional')->group(function () {
        Route::post('cycles', [CycleController::class, 'store'])->name('cycles.store');
        Route::delete('cycles/{cycle}', [CycleController::class, 'destroy'])->name('cycles.destroy');
    });

    // Stocking (Penebaran Benur) — mulai/ubah siklus oleh Owner + Operasional, semua role bisa lihat
    Route::get('stockings/{stocking}', [StockingController::class, 'show'])->name('stockings.show');
    Route::middleware('role:owner,operasional')->group(function () {
        Route::get('ponds/{pond}/stockings/create', [StockingController::class, 'create'])->name('ponds.stockings.create');
        Route::post('ponds/{pond}/stockings', [StockingController::class, 'store'])->name('ponds.stockings.store');
        Route::get('stockings/{stocking}/edit', [StockingController::class, 'edit'])->name('stockings.edit');
        Route::put('stockings/{stocking}', [StockingController::class, 'update'])->name('stockings.update');
    });

    Route::prefix('stockings/{stocking}')->name('stockings.')->group(function () {
        // Pakan & Kualitas Air Harian — input oleh Operasional (petugas lapangan) & Analis (mortalitas), semua role bisa lihat
        Route::get('daily-logs', [DailyLogController::class, 'index'])->name('daily-logs.index');
        Route::middleware('role:operasional,analis')->group(function () {
            Route::get('daily-logs/create', [DailyLogController::class, 'create'])->name('daily-logs.create');
            Route::post('daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store');
            Route::get('daily-logs/{dailyLog}/edit', [DailyLogController::class, 'edit'])->name('daily-logs.edit');
            Route::put('daily-logs/{dailyLog}', [DailyLogController::class, 'update'])->name('daily-logs.update');
        });

        // Kualitas Air Mingguan — input oleh Lab, semua role bisa lihat
        // TEMP TESTING (2026-07-04): ditambah 'operasional' biar 1 akun bisa testing semua menu.
        // BALIKIN ke 'role:lab' saja sebelum dipakai beneran / sebelum deploy.
        Route::get('water-quality-weekly', [WaterQualityWeeklyController::class, 'index'])->name('water-quality-weekly.index');
        Route::middleware('role:lab,operasional')->group(function () {
            Route::get('water-quality-weekly/create', [WaterQualityWeeklyController::class, 'create'])->name('water-quality-weekly.create');
            Route::post('water-quality-weekly', [WaterQualityWeeklyController::class, 'store'])->name('water-quality-weekly.store');
            Route::get('water-quality-weekly/{waterQualityWeekly}/edit', [WaterQualityWeeklyController::class, 'edit'])->name('water-quality-weekly.edit');
            Route::put('water-quality-weekly/{waterQualityWeekly}', [WaterQualityWeeklyController::class, 'update'])->name('water-quality-weekly.update');
        });

        // Sampling & Pertumbuhan — input oleh Analis, semua role bisa lihat
        // TEMP TESTING (2026-07-04): ditambah 'operasional' biar 1 akun bisa testing semua menu.
        // BALIKIN ke 'role:analis' saja sebelum dipakai beneran / sebelum deploy.
        Route::get('samplings', [SamplingController::class, 'index'])->name('samplings.index');
        Route::middleware('role:analis,operasional')->group(function () {
            Route::get('samplings/create', [SamplingController::class, 'create'])->name('samplings.create');
            Route::post('samplings', [SamplingController::class, 'store'])->name('samplings.store');
            Route::get('samplings/{sampling}/edit', [SamplingController::class, 'edit'])->name('samplings.edit');
            Route::put('samplings/{sampling}', [SamplingController::class, 'update'])->name('samplings.update');
        });

        // Aplikasi Kimia & Biologi (termasuk pembelian pakan) — input oleh Operasional, sesuai PRD Bagian 3 ("obat-obatan")
        Route::get('inventory-usage', [InventoryUsageController::class, 'index'])->name('inventory-usage.index');
        Route::middleware('role:operasional')->group(function () {
            Route::get('inventory-usage/create', [InventoryUsageController::class, 'create'])->name('inventory-usage.create');
            Route::post('inventory-usage', [InventoryUsageController::class, 'store'])->name('inventory-usage.store');
            Route::get('inventory-usage/{inventoryUsage}/edit', [InventoryUsageController::class, 'edit'])->name('inventory-usage.edit');
            Route::put('inventory-usage/{inventoryUsage}', [InventoryUsageController::class, 'update'])->name('inventory-usage.update');
            Route::delete('inventory-usage/{inventoryUsage}', [InventoryUsageController::class, 'destroy'])->name('inventory-usage.destroy');
        });

        // Panen (multi-tahap) — kelola oleh Owner + Operasional, konsisten dengan setup/milestone lain
        Route::get('harvests', [HarvestController::class, 'index'])->name('harvests.index');
        Route::middleware('role:owner,operasional')->group(function () {
            Route::get('harvests/create', [HarvestController::class, 'create'])->name('harvests.create');
            Route::post('harvests', [HarvestController::class, 'store'])->name('harvests.store');
            Route::get('harvests/{harvest}/edit', [HarvestController::class, 'edit'])->name('harvests.edit');
            Route::put('harvests/{harvest}', [HarvestController::class, 'update'])->name('harvests.update');
        });

        // Emergency & Kesehatan (gabungan) — semua role operasional bisa mencatat kejadian
        Route::get('emergency-logs', [EmergencyLogController::class, 'index'])->name('emergency-logs.index');
        Route::middleware('role:owner,operasional,analis')->group(function () {
            Route::get('emergency-logs/create', [EmergencyLogController::class, 'create'])->name('emergency-logs.create');
            Route::post('emergency-logs', [EmergencyLogController::class, 'store'])->name('emergency-logs.store');
        });

        // Manajemen Dasar Tambak (Pencatatan Dasar: siphon/lumpur/kincir) — input oleh Operasional & Analis, semua role bisa lihat
        Route::get('pond-maintenance-logs', [PondMaintenanceLogController::class, 'index'])->name('pond-maintenance-logs.index');
        Route::middleware('role:operasional,analis')->group(function () {
            Route::get('pond-maintenance-logs/create', [PondMaintenanceLogController::class, 'create'])->name('pond-maintenance-logs.create');
            Route::post('pond-maintenance-logs', [PondMaintenanceLogController::class, 'store'])->name('pond-maintenance-logs.store');
            Route::get('pond-maintenance-logs/{pondMaintenanceLog}/edit', [PondMaintenanceLogController::class, 'edit'])->name('pond-maintenance-logs.edit');
            Route::put('pond-maintenance-logs/{pondMaintenanceLog}', [PondMaintenanceLogController::class, 'update'])->name('pond-maintenance-logs.update');
        });

        // Biaya & Laporan — read-only, semua role bisa lihat
        Route::get('report', [ReportController::class, 'show'])->name('report.show');
    });
});

require __DIR__.'/auth.php';
