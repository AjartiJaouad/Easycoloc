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

                <form action="{{ route('expenses.store', $colocation->id) }}" method="POST" class="flex flex-wrap gap-4 items-center">
                    @csrf

                    <input type="text" name="title" placeholder="Quoi ? (Ex: Courses)" required
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1">

                    <input type="number" name="amount" step="0.01" placeholder="Combien ? (€/MAD)" required
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-32">

                    <input type="date" name="spent_at" required
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

                    <select name="category_id" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="" disabled selected>Catégorie</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Ajouter
                    </button>
                </form>
            </div>

            {{-- Liste des dépenses --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Historique des dépenses</h3>

                @if ($expenses->isEmpty())
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
                                @foreach ($expenses as $expense)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500">
                                            {{ \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-blue-600">{{ $expense->user->name }}</td>
                                        <td class="px-4 py-3 text-gray-800">{{ $expense->title }}</td>
                                        <td class="px-4 py-3 text-gray-500">
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                                {{ $expense->category->name ?? 'Autre' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-gray-900 text-right">
                                            {{ number_format($expense->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($expense->user_id === auth()->id())
                                                <form action="{{ route('expenses.destroy', [$colocation, $expense]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Supprimer cette dépense ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-500 hover:text-red-700 font-semibold text-xs">Supprimer</button>
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
