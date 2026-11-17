<?php

use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MigrationController;
use App\Http\Controllers\Admin\MonthlyRegisterController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PenaltyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\Member\MemberPortalController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{role}/{memberIndex?}', [AuthController::class, 'quickDemoLogin'])->name('demo.login');

// Admin Portal Routes
Route::prefix('admin')->middleware(['web', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::get('/members/{member}/statement', [MemberController::class, 'statement'])->name('members.statement');
    Route::resource('members', MemberController::class);

    // Loans
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);

    // Monthly Registers (Physical Register Replica)
    Route::get('/registers', [MonthlyRegisterController::class, 'index'])->name('registers.index');
    Route::post('/registers', [MonthlyRegisterController::class, 'store'])->name('registers.store');
    Route::get('/registers/{monthlyRegister}', [MonthlyRegisterController::class, 'show'])->name('registers.show');
    Route::post('/registers/dues/{due}/update', [MonthlyRegisterController::class, 'updateDue'])->name('registers.update_due');
    Route::get('/registers/{monthlyRegister}/print', [MonthlyRegisterController::class, 'print'])->name('registers.print');
    Route::get('/registers/{monthlyRegister}/export', [MonthlyRegisterController::class, 'exportExcel'])->name('registers.export');

    // Collections & Cash Payments
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/receipt/{payment}', [CollectionController::class, 'receipt'])->name('collections.receipt');
    Route::get('/collections/receipt/{payment}/print', [CollectionController::class, 'printReceipt'])->name('collections.print_receipt');

    // Penalties
    Route::get('/penalties', [PenaltyController::class, 'index'])->name('penalties.index');
    Route::post('/penalties', [PenaltyController::class, 'store'])->name('penalties.store');
    Route::post('/penalties/{penalty}/waive', [PenaltyController::class, 'waive'])->name('penalties.waive');

    // Reports & Accounting
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Existing Data Migration
    Route::get('/migration', [MigrationController::class, 'index'])->name('migration.index');
    Route::get('/migration/template', [MigrationController::class, 'downloadTemplate'])->name('migration.template');
    Route::post('/migration/upload', [MigrationController::class, 'upload'])->name('migration.upload');
    Route::post('/migration/process', [MigrationController::class, 'process'])->name('migration.process');

    // Notifications & Reminders
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');

    // System Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// Member Portal Routes
Route::prefix('member')->middleware(['web', 'member'])->name('member.')->group(function () {
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/deposits', [MemberPortalController::class, 'deposits'])->name('deposits');
    Route::get('/loans', [MemberPortalController::class, 'loans'])->name('loans');
    Route::get('/payments', [MemberPortalController::class, 'payments'])->name('payments');
    Route::get('/receipt/{payment}', [MemberPortalController::class, 'showReceipt'])->name('receipt');
    Route::get('/statement', [MemberPortalController::class, 'statement'])->name('statement');
    Route::get('/pay-online', [MemberPortalController::class, 'payOnline'])->name('pay_online');
    Route::post('/pay-online', [MemberPortalController::class, 'processOnlinePayment'])->name('process_online_payment');
    Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [MemberPortalController::class, 'updateProfile'])->name('update_profile');
});

// Deployment & Remote Database Initialization Routes (No session/CSRF requirement so it runs on fresh DB)
Route::prefix('deploy')
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ])
    ->name('deploy.')
    ->group(function () {
        Route::get('/', [DeploymentController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/migrate-seed', [DeploymentController::class, 'migrateAndSeed'])->name('migrate_seed');
        Route::match(['get', 'post'], '/migrate', [DeploymentController::class, 'migrate'])->name('migrate');
        Route::match(['get', 'post'], '/seed', [DeploymentController::class, 'seed'])->name('seed');
        Route::match(['get', 'post'], '/optimize', [DeploymentController::class, 'optimize'])->name('optimize');
    });
