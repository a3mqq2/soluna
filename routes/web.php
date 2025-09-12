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
    | Dashboard & Auth
    |--------------------------------------------------------------------------
    */
 // لوحة التحكم
 Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//  logpout


    Route::resource('services', ServiceController::class);

    // عمليات إضافية
    Route::post('services/{service}/toggle', [ServiceController::class, 'toggle'])
        ->name('services.toggle');

    Route::post('services/bulk-action', [ServiceController::class, 'bulkAction'])
        ->name('services.bulk-action');


        Route::resource('customers', CustomerController::class);

    Route::post('customers/bulk-action', [CustomerController::class, 'bulkAction'])
    ->name('customers.bulk-action');
    

    Route::get('/treasury', [TreasuryController::class, 'index'])->name('treasury.index');


    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');


    


    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])
        ->name('invoices.payment');
        


        Route::prefix('invoices/{invoice}')->group(function () {
            Route::post('/expenses', [InvoiceExpenseController::class, 'store'])->name('invoices.expenses.store');
            Route::put('/expenses/{expense}', [InvoiceExpenseController::class, 'update'])->name('invoices.expenses.update');
            Route::delete('/expenses/{expense}', [InvoiceExpenseController::class, 'destroy'])->name('invoices.expenses.destroy');
        });
        

        Route::resource('coupons', CouponController::class);
        Route::post('coupons/{coupon}/toggle', [CouponController::class, 'toggle'])
            ->name('coupons.toggle');
        
        // API Routes for coupons
        Route::post('/api/validate-coupon', [CouponController::class, 'validateCoupon']);
        Route::get('/api/generate-coupon-code', [CouponController::class, 'generateCode']);

        
        Route::get('/api/customers', [InvoiceController::class, 'getCustomers']);
        Route::get('/api/services', [InvoiceController::class, 'getServices']);
        Route::post('/api/customers', [InvoiceController::class, 'storeCustomer']);
        Route::get('/api/next-invoice-number', [InvoiceController::class, 'getNextInvoiceNumber']);
        Route::get('/api/customers/search', [InvoiceController::class, 'searchCustomers']);


        Route::post('/invoices/{invoice}/payment', [InvoicePaymentController::class, 'store'])
        ->name('invoices.payments.store');
    
    Route::get('/invoices/{invoice}/payments', [InvoicePaymentController::class, 'show'])
        ->name('invoices.payments.show');
    
    Route::patch('/payments/{payment}/status', [InvoicePaymentController::class, 'updateStatus'])
        ->name('payments.update-status');
    
    Route::delete('/payments/{payment}', [InvoicePaymentController::class, 'destroy'])
        ->name('payments.destroy');
    
    Route::get('/payments/statistics', [InvoicePaymentController::class, 'statistics'])
        ->name('payments.statistics');



        // Add this route for payment receipt
        Route::get('/payments/{payment}/receipt', [InvoicePaymentController::class, 'receipt'])
        ->name('payments.receipt');

        Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');


    Route::get('/api/invoices/{invoice}', [InvoiceController::class, 'showApi'])->name('invoices.show.api');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');

 Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
});