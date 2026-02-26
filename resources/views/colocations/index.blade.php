<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une nouvelle Colocation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('colocations.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nom de la Colocation')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('colocations.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Annuler
                            </a>
                            <x-primary-button>
                                {{ __('Créer') }}
                            </x-primary-button>
                        </div>
                    </form>
                    <form action="{{ route('colocations.invite', $colocation) }}" method="POST"
                        class="mt-3 flex gap-2">
                        @csrf
                        <input type="email" name="email" placeholder="Email de votre ami..." required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md text-sm transition">
                            Inviter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
