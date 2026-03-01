<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request, Colocation $colocation)
    {
        if (!$colocation->users->contains(auth()->id())) {
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

        // --- الحل هنا: جلب الكاتيكوريات ---
        $categories = DB::table('categories')->where('colocation_id', $colocation->id)->get();

        // إيلا كانت القائمة خاوية، كنكرييو كاتيكوريات ديفو لهاد الدار
        if ($categories->isEmpty()) {
            $defaultCats = ['Loyer', 'Nourriture', 'Électricité', 'Internet'];
            foreach ($defaultCats as $cat) {
                DB::table('categories')->insert([
                    'name' => $cat,
                    'colocation_id' => $colocation->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // كنعاودو نجبدوهم باش يمشيو للـ Blade
            $categories = DB::table('categories')->where('colocation_id', $colocation->id)->get();
        }

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'selectedMonth', 'months'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        if (!$colocation->users->contains(auth()->id())) {
            abort(403, 'Accès refusé.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.1',
            'spent_at'    => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $colocation->expenses()->create([
            'title'       => $validated['title'],
            'amount'      => $validated['amount'],
            'spent_at'    => $validated['spent_at'],
            'category_id' => $validated['category_id'],
            'user_id'     => auth()->id(),
        ]);

        return redirect()->route('expenses.index', $colocation)->with('success', 'Dépense ajoutée !');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }
        $expense->delete();
        return redirect()->back()->with('success', 'Dépense supprimée.');
    }
}
