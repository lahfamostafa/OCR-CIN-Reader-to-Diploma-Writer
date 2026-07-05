<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-university-primary leading-tight">{{ __('Créer un Nouveau Modèle de Diplôme AUI') }}</h2>
    </x-slot>

    <div class="py-12 bg-university-light/20 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <form method="POST" action="{{ route('admin.templates.store') }}" class="space-y-6">
                    @csrf
                    @include('admin.templates._form', ['template' => null])

                    <div class="flex justify-end gap-4 border-t border-gray-100 pt-4">
                        <a href="{{ route('admin.templates.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 self-center transition-colors">Annuler</a>
                        <button type="submit" class="px-5 py-2.5 bg-university-primary text-white text-xs font-bold rounded-lg shadow hover:bg-university-dark transition">
                            Créer le Modèle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>