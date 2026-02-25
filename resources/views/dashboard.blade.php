<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Flash message -->
            @if (session('error'))
                <div style="background-color:#f8d7da; color:#842029; padding:10px; border-radius:5px; margin-bottom:10px;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    @if (auth()->user() && auth()->user()->is_global_admin)
                        <p class="mt-2 font-semibold text-green-600">{{ __('Welcome Global Admin!') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
