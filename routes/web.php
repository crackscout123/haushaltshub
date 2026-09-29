<?php

use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    // Households
    Route::get('/households', [HouseholdController::class, 'index'])->name('households.index');
    Route::get('/households/create', [HouseholdController::class, 'create'])->name('households.create');
    Route::post('/households', [HouseholdController::class, 'store'])->name('households.store');
    Route::post('/households/join', [HouseholdController::class, 'join'])->name('households.join');
    Route::get('/households/{household}/dashboard', [HouseholdController::class, 'dashboard'])->name('households.dashboard');
    Route::post('/households/{household}/regenerate-code', [HouseholdController::class, 'regenerateCode'])->name('households.regenerate-code');

    // Transactions
    Route::get('/households/{household}/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/households/{household}/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/households/{household}/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/households/{household}/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
});

require __DIR__.'/auth.php';
