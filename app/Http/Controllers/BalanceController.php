<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index(Colocation $colocation)
    {
        if (! $colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $users = $colocation->users;
        $totalExpenses = $colocation->expenses()->sum('amount');
        $userCount = $users->count();

        $balances = [];

        if ($userCount > 0 && $totalExpenses > 0) {
            $average = $totalExpenses / $userCount; // حق كل واحد شحال خصو يخلص

            foreach ($users as $user) {
                $paid = $colocation->expenses()->where('user_id', $user->id)->sum('amount');

                $balance = $paid - $average;

                $balances[] = [
                    'user' => $user->name,
                    'paid' => $paid,
                    'balance' => $balance
                ];
            }
        } else {
            $average = 0;
        }

        return view('balances.index', compact('colocation', 'balances', 'totalExpenses', 'average'));
    }
}
