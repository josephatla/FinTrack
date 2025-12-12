<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UpgradeController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'id'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('accounts', AccountController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class);

    Route::get('/pricing', function () {
        return view('pricing');
    })->name('pricing');
    Route::post('/upgrade/instant', [UpgradeController::class, 'instantUpgrade'])
         ->name('upgrade.instant');

    Route::middleware(['premium'])->group(function () {
        Route::resource('budgets', BudgetController::class);
    });

    Route::resource('accounts', AccountController::class)->only(['index', 'show']);
    Route::get('/accounts/{account}/transactions', [AccountController::class, 'showTransactions'])
        ->name('accounts.transactions');
});

require __DIR__.'/auth.php';