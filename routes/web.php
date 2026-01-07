<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TreasuryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ShippingTypeController;
use App\Http\Controllers\DeliveryPriceController;
use App\Http\Controllers\InvoiceExpenseController;
use App\Http\Controllers\InvoicePaymentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard
Route::redirect('/', '/dashboard');

// Authentication Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'submit'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware(['permission:users.create'])->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['permission:users.edit'])->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
    Route::middleware(['permission:users.delete'])->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Services Management
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:services.view'])->group(function () {
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
    });
    Route::middleware(['permission:services.create'])->group(function () {
        Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
    });
    Route::middleware(['permission:services.edit'])->group(function () {
        Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::post('services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');
    });
    Route::middleware(['permission:services.delete'])->group(function () {
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::post('services/bulk-action', [ServiceController::class, 'bulkAction'])->name('services.bulk-action');
    });

    /*
    |--------------------------------------------------------------------------
    | Customers Management
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:customers.view'])->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });
    Route::middleware(['permission:customers.create'])->group(function () {
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    });
    Route::middleware(['permission:customers.edit'])->group(function () {
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    });
    Route::middleware(['permission:customers.delete'])->group(function () {
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('customers/bulk-action', [CustomerController::class, 'bulkAction'])->name('customers.bulk-action');
    });

    /*
    |--------------------------------------------------------------------------
    | Treasury & Transactions
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:treasury.view'])->group(function () {
        Route::get('/treasury', [TreasuryController::class, 'index'])->name('treasury.index');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    });
    Route::middleware(['permission:treasury.create'])->group(function () {
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Invoices Management
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:invoices.view'])->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'show'])->name('invoices.payments.show');
        Route::get('/api/invoices/{invoice}', [InvoiceController::class, 'showApi'])->name('invoices.show.api');
        Route::get('/payments/{payment}/receipt', [InvoicePaymentController::class, 'receipt'])->name('payments.receipt');
    });
    Route::middleware(['permission:invoices.create'])->group(function () {
        Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('/invoices/{invoice}/payment', [InvoicePaymentController::class, 'store'])->name('invoices.payments.store');
        Route::post('/invoices/{invoice}/expenses', [InvoiceExpenseController::class, 'store'])->name('invoices.expenses.store');
    });
    Route::middleware(['permission:invoices.edit'])->group(function () {
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::put('/invoices/{invoice}/expenses/{expense}', [InvoiceExpenseController::class, 'update'])->name('invoices.expenses.update');
        Route::patch('/payments/{payment}/status', [InvoicePaymentController::class, 'updateStatus'])->name('payments.update-status');
    });
    Route::middleware(['permission:invoices.delete'])->group(function () {
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::delete('/invoices/{invoice}/expenses/{expense}', [InvoiceExpenseController::class, 'destroy'])->name('invoices.expenses.destroy');
        Route::delete('/payments/{payment}', [InvoicePaymentController::class, 'destroy'])->name('payments.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Coupons Management
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:coupons.view'])->group(function () {
        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    });
    Route::middleware(['permission:coupons.create'])->group(function () {
        Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
    });
    Route::middleware(['permission:coupons.edit'])->group(function () {
        Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::post('coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');
    });
    Route::middleware(['permission:coupons.delete'])->group(function () {
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/payments/statistics', [InvoicePaymentController::class, 'statistics'])->name('payments.statistics');
    });

    /*
    |--------------------------------------------------------------------------
    | API Routes (for Vue components)
    |--------------------------------------------------------------------------
    */
    Route::post('/api/validate-coupon', [CouponController::class, 'validateCoupon']);
    Route::get('/api/generate-coupon-code', [CouponController::class, 'generateCode']);
    Route::get('/api/customers', [InvoiceController::class, 'getCustomers']);
    Route::get('/api/services', [InvoiceController::class, 'getServices']);
    Route::post('/api/customers', [InvoiceController::class, 'storeCustomer']);
    Route::get('/api/next-invoice-number', [InvoiceController::class, 'getNextInvoiceNumber']);
    Route::get('/api/customers/search', [InvoiceController::class, 'searchCustomers']);

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
