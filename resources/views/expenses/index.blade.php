<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Dashboard - {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-6">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
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
                <h3 class="text-lg font-semibold mb-4">Résumé</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-xl font-bold">{{ number_format($expenses->sum('amount'), 2, ',', ' ') }} MAD</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p class="text-xl font-bold">{{ $expenses->count() }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Moyenne</p>
                        <p class="text-xl font-bold">
                            {{ $expenses->count() > 0 ? number_format($expenses->avg('amount'), 2, ',', ' ') : 0 }} MAD
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Ajouter une dépense</h3>

                <form action="{{ route('expenses.store', $colocation->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf

                    <input type="text" name="title" placeholder="Description" value="{{ old('title') }}" required
                        class="border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <input type="number" name="amount" step="0.01" placeholder="Montant" value="{{ old('amount') }}" required
                        class="border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <input type="date" name="spent_at" value="{{ old('spent_at', date('Y-m-d')) }}" required
                        class="border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <select name="category_id" required class="border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Catégorie...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700 transition font-bold">
                        Ajouter
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">Historique</h3>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-sm text-gray-600">
                        <tr>
                            <th class="p-3">Date</th>
                            <th class="p-3">Description</th>
                            <th class="p-3">Utilisateur</th>
                            <th class="p-3 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-medium">{{ $expense->title }}</td>
                                <td class="p-3 text-sm text-gray-500">{{ $expense->user->name }}</td>
                                <td class="p-3 text-right font-semibold text-indigo-600">
                                    {{ number_format($expense->amount, 2, ',', ' ') }} MAD
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t">
                                <td colspan="4" class="p-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Aucune dépense trouvée pour ce mois.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
