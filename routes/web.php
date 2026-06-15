<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\BranchSwitchController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Enterprise\BranchController;
use App\Http\Controllers\Enterprise\CompanyController;
use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Pricing\PricingRuleController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Rooms\AmenityController;
use App\Http\Controllers\Rooms\FloorController;
use App\Http\Controllers\Rooms\RoomController;
use App\Http\Controllers\Rooms\RoomTypeController;
use App\Http\Controllers\Security\LoginLogController;
use App\Http\Controllers\Security\SessionController;
use App\Http\Controllers\Services\ServiceCategoryController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\System\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::get('two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('two-factor', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

Route::middleware(['auth', 'active', 'branch.context'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('branch/switch', [BranchSwitchController::class, 'switch'])->name('branch.switch');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Account Management Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
        Route::get('edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::get('security', [\App\Http\Controllers\ProfileController::class, 'security'])->name('security');
        Route::put('password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
        Route::post('logout-others', [\App\Http\Controllers\ProfileController::class, 'logoutOtherSessions'])->name('logout-others');
        Route::get('notifications', [\App\Http\Controllers\ProfileController::class, 'notifications'])->name('notifications');
        Route::put('notifications', [\App\Http\Controllers\ProfileController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('login-history', [\App\Http\Controllers\ProfileController::class, 'loginHistory'])->name('login-history');
        Route::delete('sessions/{sessionId}', [\App\Http\Controllers\ProfileController::class, 'logoutSession'])->name('sessions.logout');
    });

    // 2FA Routes
    Route::prefix('2fa')->name('two-factor.')->group(function () {
        Route::get('setup', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'setup'])->name('setup');
        Route::post('enable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'enable'])->name('enable');
        Route::post('disable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'disable'])->name('disable');
        Route::get('backup-codes', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'backupCodes'])->name('backup-codes');
    });

    Route::get('security/login-logs', [LoginLogController::class, 'index'])
        ->name('security.login-logs.index')
        ->middleware('permission:security.view');
    Route::get('security/sessions', [SessionController::class, 'index'])
        ->name('security.sessions.index')
        ->middleware('permission:security.view');
    Route::delete('security/sessions/{id}', [SessionController::class, 'destroy'])
        ->name('security.sessions.destroy')
        ->middleware('permission:security.view');

    Route::prefix('enterprise')->name('enterprise.')->group(function () {
        Route::resource('companies', CompanyController::class)->middleware('permission:enterprise.view');
        Route::resource('branches', BranchController::class)->middleware('permission:enterprise.view');
        Route::resource('departments', \App\Http\Controllers\Enterprise\DepartmentController::class)->middleware('permission:enterprise.view');
        Route::resource('suppliers', \App\Http\Controllers\Enterprise\SupplierController::class)->middleware('permission:enterprise.view');
        Route::resource('bank-accounts', \App\Http\Controllers\Enterprise\BankAccountController::class)->middleware('permission:enterprise.view');
        Route::resource('taxes', \App\Http\Controllers\Enterprise\TaxController::class)->middleware('permission:enterprise.view');
        Route::resource('service-fees', \App\Http\Controllers\Enterprise\ServiceFeeController::class)->middleware('permission:enterprise.view');
    });

    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::resource('room-types', RoomTypeController::class)->middleware('permission:rooms.view');
        Route::get('floors/map', [FloorController::class, 'map'])->name('floors.map')->middleware('permission:rooms.view');
        Route::resource('floors', FloorController::class)->middleware('permission:rooms.view');
        Route::resource('rooms', RoomController::class)->middleware('permission:rooms.view');
        Route::resource('amenities', AmenityController::class)->middleware('permission:rooms.view');
    });

    Route::resource('customers', CustomerController::class)->middleware('permission:customers.view');

    Route::prefix('services')->name('services.')->group(function () {
        Route::resource('categories', ServiceCategoryController::class)->middleware('permission:services.view');
        Route::resource('items', ServiceController::class)->middleware('permission:services.view');
    });

    Route::prefix('system')->name('system.')->group(function () {
        Route::resource('users', UserController::class)->middleware('permission:system.view');
        Route::get('configs', [\App\Http\Controllers\System\SystemConfigController::class, 'index'])->name('configs.index')->middleware('permission:system.view');
        Route::get('configs/create', [\App\Http\Controllers\System\SystemConfigController::class, 'create'])->name('configs.create')->middleware('permission:system.create');
        Route::post('configs', [\App\Http\Controllers\System\SystemConfigController::class, 'store'])->name('configs.store')->middleware('permission:system.create');
        Route::get('configs/{config}/edit', [\App\Http\Controllers\System\SystemConfigController::class, 'edit'])->name('configs.edit')->middleware('permission:system.update');
        Route::put('configs/{config}', [\App\Http\Controllers\System\SystemConfigController::class, 'update'])->name('configs.update')->middleware('permission:system.update');
        Route::get('activity-logs', [\App\Http\Controllers\System\ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:system.view');
        Route::get('notifications', [\App\Http\Controllers\System\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('backups', [\App\Http\Controllers\System\BackupController::class, 'index'])->name('backups.index')->middleware('permission:system.view');
        Route::post('backups', [\App\Http\Controllers\System\BackupController::class, 'store'])->name('backups.store')->middleware('permission:system.create');
    });

    Route::get('bookings/availability', [BookingController::class, 'availability'])
        ->name('bookings.availability')
        ->middleware('permission:bookings.view');

    Route::resource('bookings', BookingController::class)->middleware('permission:bookings.view');
    Route::post('bookings/{booking}/check-in', [BookingController::class, 'checkIn'])
        ->name('bookings.check-in')
        ->middleware('permission:bookings.update');
    Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])
        ->name('bookings.check-out')
        ->middleware('permission:bookings.update');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel')
        ->middleware('permission:bookings.update');
    Route::post('bookings/{booking}/extend', [BookingController::class, 'extend'])
        ->name('bookings.extend')
        ->middleware('permission:bookings.update');
    Route::post('bookings/{booking}/change-room', [BookingController::class, 'changeRoom'])
        ->name('bookings.change-room')
        ->middleware('permission:bookings.update');
    Route::post('bookings/{booking}/services', [BookingController::class, 'addService'])
        ->name('bookings.services')
        ->middleware('permission:bookings.update');

    Route::get('invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index')
        ->middleware('permission:payments.view');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show')
        ->middleware('permission:payments.view');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])
        ->name('invoices.pdf')
        ->middleware('permission:payments.print');
    Route::get('invoices/{invoice}/pay', [PaymentController::class, 'create'])
        ->name('payments.create')
        ->middleware('permission:payments.create');
    Route::post('invoices/{invoice}/pay', [PaymentController::class, 'store'])
        ->name('payments.store')
        ->middleware('permission:payments.create');
    Route::get('payments/callback', [PaymentController::class, 'callback'])
        ->name('payments.callback');

    Route::get('pricing-rules', [PricingRuleController::class, 'index'])
        ->name('pricing-rules.index')
        ->middleware('permission:pricing.view');
    Route::post('pricing-rules', [PricingRuleController::class, 'storeRule'])
        ->name('pricing-rules.store')
        ->middleware('permission:pricing.create');
    Route::delete('pricing-rules/{pricingRule}', [PricingRuleController::class, 'destroyRule'])
        ->name('pricing-rules.destroy')
        ->middleware('permission:pricing.delete');
    Route::post('pricing-rules/seasonal', [PricingRuleController::class, 'storeSeasonal'])
        ->name('pricing-rules.seasonal.store')
        ->middleware('permission:pricing.create');
    Route::delete('pricing-rules/seasonal/{seasonalRate}', [PricingRuleController::class, 'destroySeasonal'])
        ->name('pricing-rules.seasonal.destroy')
        ->middleware('permission:pricing.delete');

    Route::get('reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('permission:reports.view');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel')
        ->middleware('permission:reports.export');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf')
        ->middleware('permission:reports.export');

    Route::resource('luggage', \App\Http\Controllers\Luggage\LuggageController::class)->middleware('permission:luggage.view');

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::resource('products', \App\Http\Controllers\Inventory\ProductController::class)->middleware('permission:inventory.view');
    });

    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::resource('requests', \App\Http\Controllers\Maintenance\MaintenanceRequestController::class)
            ->middleware('permission:maintenance.view')
            ->parameters(['requests' => 'maintenanceRequest']);
    });

    Route::resource('contracts', \App\Http\Controllers\Contracts\ContractController::class)->middleware('permission:contracts.view');
});

Route::middleware(['auth', 'active', 'customer'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Portal\PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('bookings', [\App\Http\Controllers\Portal\PortalController::class, 'bookings'])->name('bookings.index');
    Route::get('bookings/create', [\App\Http\Controllers\Portal\PortalController::class, 'createBooking'])->name('bookings.create');
    Route::post('bookings', [\App\Http\Controllers\Portal\PortalController::class, 'storeBooking'])->name('bookings.store');
    Route::get('bookings/{booking}', [\App\Http\Controllers\Portal\PortalController::class, 'showBooking'])->name('bookings.show');
});
