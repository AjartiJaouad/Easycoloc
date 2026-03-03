<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BalanceController;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class)
         ->except(['show']);
});

Route::get('/dashboard', function () {
    $user = auth()->user;
    if ($user->is_global_admin) {
        $stats = [
            'total_users'   => User::count(),
            'active_colocs' => Colocation::where('status', 'active')->count(),
            'total_spent'   => Expense::sum('amount') ?? 0,
        ];

        $recent_users = User::latest()->take(10)->get();

        return view('dashboard', [
            'stats' => $stats,
            'recent_users' => $recent_users
        ]);
    }
    return view('dashboard', [
        'stats' => null,
        'recent_users' => collect()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


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

    // Balances & Settlements
    Route::get('/colocations/{colocation}/balances', [BalanceController::class, 'index'])->name('balances.index');

    Route::patch('/settlements/{settlement}/pay', [BalanceController::class, 'markAsPaid'])->name('settlements.mark_paid');

});

require __DIR__.'/auth.php';
