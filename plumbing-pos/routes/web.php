
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockReceiptController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\MpesaController;


// ==================================================
// PUBLIC ROUTES
// ==================================================

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

// M-Pesa callback: Safaricom must access this without login.
// The callback token is checked by MpesaController.
Route::post(
    '/api/mpesa/callback/{token}',
    [MpesaController::class, 'callback']
)->withoutMiddleware([
    ValidateCsrfToken::class,
])->name('mpesa.callback');


// ==================================================
// PROTECTED ROUTES: AUTHENTICATED USERS ONLY
// ==================================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,cashier')
        ->name('dashboard');


    // ==================================================
    // ADMIN-ONLY MODULES
    // ==================================================

    Route::middleware('role:admin')->group(function () {

        Route::resource('products', ProductController::class);

        Route::resource('categories', CategoryController::class);

        Route::resource('suppliers', SupplierController::class);

        Route::get('/stock', [StockController::class, 'index'])
            ->name('stock.index');

        Route::get('/stock/receive', [
            StockReceiptController::class,
            'create'
        ])->name('stock.receive.create');

        Route::post('/stock/receive', [
            StockReceiptController::class,
            'store'
        ])->name('stock.receive.store');

        Route::get('/stock-movements', [
            StockMovementController::class,
            'index'
        ])->name('stock-movements.index');

        Route::get('/reports/sales', [
            SalesReportController::class,
            'index'
        ])->name('reports.sales');

        Route::resource('expenses', ExpenseController::class)
            ->except(['show']);

        Route::resource('users', UserController::class)
            ->except(['show', 'destroy']);

        Route::get('/audit-logs', [
            AuditLogController::class,
            'index'
        ])->name('audit-logs.index');
    });


    // ==================================================
    // SALES AND RETURNS: ADMIN AND CASHIER
    // ==================================================

    Route::middleware('role:admin,cashier')->group(function () {

        Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

        Route::get('/sales/create', [SaleController::class, 'create'])
            ->name('sales.create');

        Route::post('/sales', [SaleController::class, 'store'])
            ->name('sales.store');

        Route::get('/sales/{sale}/receipt', [
            SaleController::class,
            'receipt'
        ])->name('sales.receipt');


        // Sales returns
        Route::get('/sales-returns', [
            SaleReturnController::class,
            'index'
        ])->name('sales-returns.index');

        Route::get('/sales/{sale}/return', [
            SaleReturnController::class,
            'create'
        ])->name('sales-returns.create');

        Route::post('/sales/{sale}/return', [
            SaleReturnController::class,
            'store'
        ])->name('sales-returns.store');
        // Draft sales
        Route::get('/sales/drafts', [SaleController::class, 'drafts'])
          ->name('sales.drafts');

        Route::post('/sales/drafts', [SaleController::class, 'saveDraft'])
          ->name('sales.drafts.store');

        Route::get('/sales/{sale}/resume', [SaleController::class, 'resumeDraft'])
          ->name('sales.drafts.resume');
    });

});
