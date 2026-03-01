<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Dashboard - {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-6">

            <!-- ===== SUMMARY ===== -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Résumé</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-xl font-bold">
                            {{ number_format($expenses->sum('amount'),2,',',' ') }} €
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p class="text-xl font-bold">
                            {{ $expenses->count() }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Moyenne</p>
                        <p class="text-xl font-bold">
                            {{ $expenses->count() > 0
                                ? number_format($expenses->avg('amount'),2,',',' ')
                                : 0 }} €
                        </p>
                    </div>
                </div>
            </div>

            <!-- ===== ADD EXPENSE ===== -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Ajouter une dépense</h3>

                <form action="{{ route('expenses.store', $colocation->id) }}" method="POST"
                      class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf

                    <input type="text" name="title" placeholder="Description"
                        class="border rounded px-3 py-2">

                    <input type="number" name="amount" step="0.01" placeholder="Montant"
                        class="border rounded px-3 py-2">

                    <input type="date" name="spent_at"
                        class="border rounded px-3 py-2">

                    <button type="submit"
                        class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                        Ajouter
                    </button>
                </form>
            </div>

            <!-- ===== TABLE ===== -->
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
                        @foreach($expenses as $expense)
                            <tr class="border-t">
                                <td class="p-3">
                                    {{ \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') }}
                                </td>
                                <td class="p-3">{{ $expense->title }}</td>
                                <td class="p-3">{{ $expense->user->name }}</td>
                                <td class="p-3 text-right font-semibold">
                                    {{ number_format($expense->amount,2,',',' ') }} €
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
