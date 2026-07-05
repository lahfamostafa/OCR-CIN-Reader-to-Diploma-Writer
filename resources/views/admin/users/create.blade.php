<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nouvel utilisateur') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    @include('admin.users._form', ['user' => null])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline self-center">Annuler</a>
                        <x-primary-button>Créer</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
