<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-university-primary leading-tight">{{ __('Utilisateurs') }}</h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-university-primary text-white text-sm font-semibold rounded-lg hover:bg-university-secondary transition-colors duration-200">
                + Nouvel utilisateur
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-flash />

        <div class="bg-white rounded-xl shadow-lg overflow-hidden p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="py-3 pr-4 font-semibold">Nom</th>
                            <th class="py-3 pr-4 font-semibold">Email</th>
                            <th class="py-3 pr-4 font-semibold">Rôle</th>
                            <th class="py-3 pr-4 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $user->email }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $user->isAdmin() ? 'bg-university-light text-university-primary' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 flex gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-university-primary hover:text-university-accent font-medium transition-colors">Modifier</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-700 transition-colors">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
