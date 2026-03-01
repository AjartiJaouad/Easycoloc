<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Settlement;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Affiche l'état des comptes et les remboursements de la colocation.
     */
    public function index(Colocation $colocation)
    {
        // Vérifier si l'utilisateur appartient bien à cette colocation
        if (! $colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $users = $colocation->users;
        $totalExpenses = $colocation->expenses()->sum('amount');
        $userCount = $users->count();

        $balances = [];
        $average = 0;

        // Calculer la part de chacun et l'équilibre individuel
        if ($userCount > 0 && $totalExpenses > 0) {
            $average = $totalExpenses / $userCount;

            foreach ($users as $user) {
                // Somme payée par l'utilisateur
                $paid = $colocation->expenses()->where('user_id', $user->id)->sum('amount');

                // Différence entre ce qu'il a payé et ce qu'il doit (sa part)
                $balance = $paid - $average;

                $balances[] = [
                    'user' => $user->name,
                    'paid' => $paid,
                    'balance' => $balance
                ];
            }
        }

        // Récupérer la liste des remboursements (Settlements) enregistrés
        $settlements = Settlement::where('colocation_id', $colocation->id)
            ->with(['debtor', 'creditor'])
            ->latest()
            ->get();

        return view('balances.index', compact('colocation', 'balances', 'totalExpenses', 'average', 'settlements'));
    }

    public function markAsPaid(Settlement $settlement)
    {
        // Mettre à jour le statut du paiement en base de données
        $settlement->update([
            'is_paid' => true
        ]);

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Le remboursement a été marqué comme payé avec succès  !');
    }

    public function store(Request $request, Colocation $colocation)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        $colocation->expenses()->create([
            'user_id'     => auth()->id(),
            'description' => $data['description'],
            'amount'      => $data['amount'],
        ]);

        return redirect()->back()->with('success', 'Dépense ajoutée.');
    }
}
