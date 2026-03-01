<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Équilibres : ') }} {{ $colocation->name }}
            </h2>
            <div class="space-x-4">
                <a href="{{ route('expenses.index', $colocation) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">
                    💰 Gérer les dépenses
                </a>
                <a href="{{ route('colocations.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold">
                    &larr; Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Succès</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- 1. RÉSUMÉ DES COMPTES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest">Total des dépenses</h3>
                    <p class="text-3xl font-black text-gray-900 mt-2">{{ number_format($totalExpenses, 2, ',', ' ') }} MAD</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest">Part par personne</h3>
                    <p class="text-3xl font-black text-indigo-600 mt-2">{{ number_format($average, 2, ',', ' ') }} MAD</p>
                </div>
            </div>

            {{-- 2. ÉTAT GÉNÉRAL (Qui est en positif/négatif) --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                    État général des comptes
                </h3>

                @if (empty($balances))
                    <p class="text-gray-500 text-center py-4">Pas de dépenses pour le moment. Tout est à jour ! ✨</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($balances as $b)
                            <div class="p-4 rounded-xl border {{ $b['balance'] > 0 ? 'bg-green-50 border-green-100' : ($b['balance'] < 0 ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-200') }}">
                                <p class="font-bold text-gray-900 text-lg">{{ $b['user'] }}</p>
                                <p class="text-xs text-gray-500 mb-2 font-medium">Payé au total : {{ number_format($b['paid'], 2, ',', ' ') }} MAD</p>
                                <div class="mt-2 text-sm">
                                    @if ($b['balance'] > 0)
                                        <span class="text-green-700 font-bold">À recevoir : +{{ number_format($b['balance'], 2, ',', ' ') }} MAD</span>
                                    @elseif ($b['balance'] < 0)
                                        <span class="text-red-700 font-bold">Doit payer : {{ number_format(abs($b['balance']), 2, ',', ' ') }} MAD</span>
                                    @else
                                        <span class="text-gray-600 font-bold">À jour ✔️</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 3. REMBOURSEMENTS À EFFECTUER --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
                    Remboursements à effectuer
                </h3>

                @if(isset($settlements) && count($settlements) > 0)
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">De (doit payer)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">À (reçoit)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Statut / Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($settlements as $settlement)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold italic">
                                            {{ $settlement->debtor->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold italic">
                                            {{ $settlement->creditor->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-indigo-600">
                                            {{ number_format($settlement->amount, 2, ',', ' ') }} MAD
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if(!$settlement->is_paid)
                                                <form action="{{ route('settlements.mark_paid', $settlement->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            onclick="return confirm('Confirmer que ce remboursement a été effectué ?')"
                                                            class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 px-4 rounded-lg transition shadow-sm inline-flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Marquer comme payé
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-green-600 text-xs font-black bg-green-50 px-3 py-1.5 rounded-full border border-green-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    PAYÉ
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4 italic underline">Aucun remboursement en attente.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
