<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\SessionController;

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
    
    Route::post('/session/draft', [SessionController::class, 'storeDraft'])->name('session.draft');
    Route::post('/session/draft/budget', [SessionController::class, 'storeBudgetDraft'])->name('session.draft_budget');
    Route::post('/session/autosave', [SessionController::class, 'autosave'])->name('session.autosave');

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

    Route::get('/accounts/{account}/transactions', [AccountController::class, 'showTransactions'])
        ->name('accounts.transactions');
});

require __DIR__.'/auth.php';