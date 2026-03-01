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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ملخص الحسابات --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 text-center">
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total des dépenses</h3>
                    <p class="text-3xl font-black text-gray-800 mt-2">{{ number_format($totalExpenses, 2) }} MAD</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 text-center">
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Part par personne</h3>
                    <p class="text-3xl font-black text-indigo-600 mt-2">{{ number_format($average, 2) }} MAD</p>
                </div>
            </div>

            {{-- شكون كيتسال شكون --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">État des comptes</h3>

                @if (empty($balances))
                    <p class="text-gray-500 text-center py-4">Pas de dépenses pour le moment. Tout est nickel ! ✨</p>
                @else
                    <div class="space-y-4">
                        @foreach ($balances as $b)
                            <div class="flex flex-col sm:flex-row justify-between items-center p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <div class="mb-2 sm:mb-0 text-center sm:text-left">
                                    <p class="font-bold text-gray-800 text-lg">{{ $b['user'] }}</p>
                                    <p class="text-sm text-gray-500">A payé en tout : <span class="font-semibold">{{ number_format($b['paid'], 2) }} MAD</span></p>
                                </div>

                                <div>
                                    @if ($b['balance'] > 0)
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-bold inline-block">
                                            On lui doit : +{{ number_format($b['balance'], 2) }} MAD
                                        </span>
                                    @elseif ($b['balance'] < 0)
                                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-bold inline-block">
                                            Doit payer : {{ number_format(abs($b['balance']), 2) }} MAD
                                        </span>
                                    @else
                                        <span class="px-4 py-2 bg-gray-200 text-gray-800 rounded-full text-sm font-bold inline-block">
                                            À jour ✔️
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
