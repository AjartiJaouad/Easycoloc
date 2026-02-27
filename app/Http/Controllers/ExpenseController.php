<?php

namespace App\Http\Controllers;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Colocation $colocation)
    {
        if (! $colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $expenses = $colocation->expenses()->with('user')->orderBy('spent_at', 'desc')->get();

        return view('expenses.index', compact('colocation', 'expenses'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        if (! $colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.1',
            'spent_at' => 'required|date',
            'category' => 'required|string|max:50',
        ]);

        $colocation->expenses()->create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'spent_at' => $validated['spent_at'],
            'category' => $validated['category'],
            'user_id' => auth()->id(),
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
