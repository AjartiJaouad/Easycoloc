<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Colocations') }}
            </h2>
            <a href="{{ route('colocations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md text-sm transition">
                + nouvelle colocation
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-700 bg-green-100 p-4 rounded-lg border border-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($colocations->isEmpty())
                    <p class="text-gray-500 text-center py-4">Vous ne participez à aucune colocation pour le moment.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($colocations as $colocation)
                            <div class="border rounded-xl p-5 shadow-sm relative {{ $colocation->status === 'cancelled' ? 'bg-gray-50 border-gray-200' : 'bg-white border-blue-100' }}">

                                <h3 class="text-xl font-bold text-gray-800">{{ $colocation->name }}</h3>

                                <div class="mt-3 space-y-1">
                                    <p class="text-sm text-gray-600">
                                        Statut:
                                        <span class="{{ $colocation->status === 'active' ? 'text-green-600' : 'text-red-500' }} font-bold uppercase text-xs px-2 py-1 bg-gray-100 rounded-full">
                                            {{ $colocation->status }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Mon rôle: <span class="font-semibold uppercase">{{ $colocation->pivot->role }}</span>
                                    </p>
                                </div>

                                @if($colocation->status === 'active')
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-center">
                                        <span class="text-xs text-gray-500 block mb-1">Code d’invitation (jeton) :</span>
                                        <code class="font-mono text-blue-700 font-bold text-lg select-all">{{ $colocation->invitation_token }}</code>
                                    </div>

                                    @if($colocation->pivot->role === 'owner')
                                        <form action="{{ route('colocations.invite', $colocation) }}" method="POST" class="mt-4 border-t pt-4">
                                            @csrf
                                            <p class="text-xs text-gray-500 mb-2 font-semibold">Inviter un·e ami·e par e‑mail :</p>
                                            <div class="flex gap-2">
                                                <input type="email" name="email" placeholder="Email..." required
                                                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md text-sm transition">
                                                    Inviter
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                @endif

                                @if($colocation->pivot->role === 'owner' && $colocation->status === 'active')
                                    <form action="{{ route('colocations.cancel', $colocation) }}" method="POST" class="mt-5" onsubmit="return confirm('Annuler cette colocation ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2 px-4 border border-red-200 rounded-lg text-sm transition">
                                            Annuler la colocation
                                        </button>
                                    </form>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-6 p-4 bg-white border border-gray-200 rounded-lg">
    <form action="{{ route('colocations.join') }}" method="POST" class="flex items-center gap-4">
        @csrf
        <div class="flex-1">
            <x-text-input name="invitation_token" placeholder="Entrez le code d'invitation" class="w-full" required />
        </div>
        <x-primary-button>
            Rejoindre
        </x-primary-button>
    </form>
</div>
    </div>
</x-app-layout>
