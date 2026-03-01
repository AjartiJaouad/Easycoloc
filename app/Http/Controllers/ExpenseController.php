<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
   public function index(\Illuminate\Http\Request $request, \App\Models\Colocation $colocation)
    {
        if (! $colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $selectedMonth = $request->input('month', date('Y-m'));
        $year = substr($selectedMonth, 0, 4);
        $month = substr($selectedMonth, 5, 2);

        $expenses = $colocation->expenses()
            ->with('user')
            ->whereYear('spent_at', $year)
            ->whereMonth('spent_at', $month)
            ->orderBy('spent_at', 'desc')
            ->get();

        $categories = \Illuminate\Support\Facades\DB::table('categories')->where('colocation_id', $colocation->id)->get();

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = \Carbon\Carbon::now()->subMonths($i);

            $months[$date->format('Y-m')] = $date->format('F Y');
        }

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'selectedMonth', 'months'));
    }

   public function store(Request $request, Colocation $colocation)
{
    if (! $colocation->users->contains(auth()->id())) {
        abort(403, 'Accès refusé.');
    }

    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'amount'      => 'required|numeric|min:0.1',
        'spent_at'    => 'required|date',
        'category_id' => 'required|integer|exists:categories,id',
    ]);

    $colocation->expenses()->create([
        'title'       => $validated['title'],
        'amount'      => $validated['amount'],
        'spent_at'    => $validated['spent_at'],
        'category_id' => $validated['category_id'],
        'user_id'     => auth()->id(),
    ]);

    return redirect()->route('expenses.index', $colocation)->with('success', 'Dépense ajoutée avec succès.');
}

    public function destroy(Colocation $colocation, Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres dépenses.');
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Dépense supprimée.');
    }
}
