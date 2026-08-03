<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinancialSummaryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\TransactionDataController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\TransactionController as UserTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicReportController::class, 'index'])->name('public.report');
Route::get('/laporan/summary', [PublicReportController::class, 'summary'])->name('public.report.summary');
Route::get('/laporan/transaksi/data', TransactionDataController::class)->name('public.report.transactions.data');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/summary', FinancialSummaryController::class)->name('summary');
    Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
    Route::view('/profil', 'profile.show')->name('profile');

    Route::get('/categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

    Route::get('/transactions/data', TransactionDataController::class)->name('transactions.data');
    Route::resource('transactions', TransactionController::class)->except(['create', 'edit']);

    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class)->except(['create', 'edit']);

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function (): void {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    Route::get('/summary', FinancialSummaryController::class)->name('summary');
    Route::get('/laporan', [UserReportController::class, 'index'])->name('reports.index');
    Route::view('/profil', 'profile.show')->name('profile');
    Route::get('/transactions', [UserTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/data', TransactionDataController::class)->name('transactions.data');
});
