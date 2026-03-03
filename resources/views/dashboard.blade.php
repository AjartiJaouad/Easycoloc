<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Global Admin Panel') }}
            </h2>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold uppercase">
                System Online
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Active Households</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_colocs'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-50 rounded-lg text-amber-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Transaction Volume</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_spent'] ?? 0, 2) }} €</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Newest Members</h3>
                        <a href="#" class="text-xs text-indigo-600 hover:underline">View All Users</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Reputation</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recent_users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $user->colocations()->exists() ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $user->colocations()->exists() ? 'In House' : 'Solo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-bold">
                                        {{ $user->reputation ?? 0 }} pts
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-gray-400 hover:text-red-600">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No users found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
