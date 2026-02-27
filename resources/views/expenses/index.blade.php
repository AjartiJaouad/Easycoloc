<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dépenses : ') }} {{ $colocation->name }}
            </h2>
            <a href="{{ route('colocations.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold">
                &larr; Retour aux colocations
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Messages de succès --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulaire pour ajouter une dépense --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter une nouvelle dépense</h3>

                <form action="{{ route('expenses.store', $colocation) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        <div>
                            <x-input-label for="title" value="Quoi ? (Ex: Courses)" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="amount" value="Combien ? (€/MAD)" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.1" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="spent_at" value="Quand ?" />
                            <x-text-input id="spent_at" name="spent_at" type="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="category" value="Catégorie" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Alimentation">Alimentation</option>
                                <option value="Loyer">Loyer</option>
                                <option value="Énergie">Énergie (Eau/Élec)</option>
                                <option value="Internet">Internet</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <x-primary-button>
                            Ajouter la dépense
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Liste des dépenses --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Historique des dépenses</h3>

                @if($expenses->isEmpty())
                    <p class="text-gray-500 text-center py-4">Aucune dépense enregistrée pour le moment.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Date</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Payé par</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Description</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Catégorie</th>
                                    <th class="px-4 py-3 font-semibold text-gray-900 text-right">Montant</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 font-medium text-blue-600">{{ $expense->user->name }}</td>
                                        <td class="px-4 py-3 text-gray-800">{{ $expense->title }}</td>
                                        <td class="px-4 py-3 text-gray-500">
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ $expense->category }}</span>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-gray-900 text-right">{{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @if($expense->user_id === auth()->id())
                                                <form action="{{ route('expenses.destroy', [$colocation, $expense]) }}" method="POST" onsubmit="return confirm('Supprimer cette dépense ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs">Supprimer</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
