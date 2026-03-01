<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Dashboard - {{ $colocation->name }}
            </h2>
            <div class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold">
                ⭐ Réputation: {{ auth()->user()->reputation ?? 0 }}
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-4 rounded-lg shadow flex flex-col sm:flex-row justify-between items-center gap-4 border-l-4 border-indigo-500">
                <div class="text-gray-700 font-bold text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Filtrer les dépenses par mois :
                </div>

                <form method="GET" action="{{ route('expenses.index', $colocation) }}" class="flex w-full sm:w-auto">
                    <select name="month" onchange="this.form.submit()" class="block w-full sm:w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm cursor-pointer bg-gray-50 hover:bg-white transition">
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                                {{ ucfirst($label) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Résumé Financier</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-indigo-50 p-4 rounded-xl">
                        <p class="text-sm text-indigo-600 font-bold uppercase">Total</p>
                        <p class="text-2xl font-black text-indigo-900">{{ number_format($expenses->sum('amount'), 2, ',', ' ') }} MAD</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500 font-bold uppercase">Nombre</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $expenses->count() }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500 font-bold uppercase">Moyenne</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $expenses->count() > 0 ? number_format($expenses->avg('amount'), 2, ',', ' ') : '0,00' }} MAD
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-green-500">
                <h3 class="text-lg font-semibold mb-4">Ajouter une dépense</h3>

                <form action="{{ route('expenses.store', $colocation->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    @csrf
                    <input type="text" name="title" placeholder="Description" value="{{ old('title') }}" required
                        class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">

                    <input type="number" name="amount" step="0.01" placeholder="Montant" value="{{ old('amount') }}" required
                        class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">

                    <input type="date" name="spent_at" value="{{ old('spent_at', date('Y-m-d')) }}" required
                        class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">

                    <select name="category_id" required class="border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option value="">Catégorie...</option>
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @empty
                            <option disabled>Aucune catégorie trouvée</option>
                        @endforelse
                    </select>

                    <button type="submit" class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700 transition font-bold shadow-md">
                        + Ajouter
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Détails des dépenses</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="p-4 font-bold">Date</th>
                                <th class="p-4 font-bold">Description</th>
                                <th class="p-4 font-bold">Utilisateur</th>
                                <th class="p-4 font-bold text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($expenses as $expense)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 text-sm text-gray-600 italic">
                                        {{ \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="p-4 font-medium text-gray-800">{{ $expense->title }}</td>
                                    <td class="p-4">
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-semibold">
                                            {{ $expense->user->name }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-bold text-indigo-600">
                                        {{ number_format($expense->amount, 2, ',', ' ') }} MAD
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <p>Aucune dépense pour ce mois.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
