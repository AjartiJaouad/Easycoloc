<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BalanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin-test', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect('/login');
    }

    if (! $user->is_global_admin) {
        return redirect('/dashboard')->with('error', 'Access denied.');
    }

    return 'Welcome Global Admin!';
})->middleware('auth');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Colocation, Expenses & Balances routes
Route::middleware('auth')->group(function () {
    Route::post('/colocations/join', [ColocationController::class, 'join'])->name('colocations.join');
    Route::resource('colocations', ColocationController::class)->only(['index', 'create', 'store']);

    Route::patch('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('/colocations/{colocation}/invite', [ColocationController::class, 'sendInvitation'])->name('colocations.invite');

    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'removeMember'])->name('colocations.removeMember');

    // Expenses
    Route::get('/colocations/{colocation}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Balances
    Route::get('/colocations/{colocation}/balances', [BalanceController::class, 'index'])->name('balances.index'); 
});

require __DIR__.'/auth.php';
