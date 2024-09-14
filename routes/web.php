<?php

use App\Http\Controllers\SingleChargeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('single-charge');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Single Charge
    Route::controller(SingleChargeController::class)->group(function () {
        Route::get('/single-charge', 'index')->name('single-charge');
        Route::post('/single-charge', 'singleCharge')->name('single.charge');
    });

    // Show Balance
    Route::get('/balance', [BalanceController::class, 'getBalance'])->name('balance');

    //  Plans and Subscriptions
    Route::controller(SubscriptionController::class)->group(function () {
        // Plans
        Route::get('/plans', 'showPlans')->name('plans.index');
        Route::get('/plans/create', 'createPlans')->name('plans.create');
        Route::post('/plans/store', 'storePlans')->name('plans.store');
        Route::get('/plans/checkout/{id}', 'checkout')->name('plans.checkout');
        Route::post('/plans/process', 'process')->name('plans.process');

        // Subscriptions
        Route::get('/subscriptions', 'showSubscriptions')->name('subscriptions.index');
        Route::get('/subscriptions/cancel', 'cancelSubscriptions')->name('subscriptions.cancel');
        Route::get('/subscriptions/resume', 'resumeSubscriptions')->name('subscriptions.resume');

        // Transactions-History
        Route::get('/transactions/history', 'getTransactionHistory')->name('transactions.history');
    });
});

require __DIR__.'/auth.php';
