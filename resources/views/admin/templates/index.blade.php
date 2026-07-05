<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-university-primary leading-tight">{{ __('Modèles de diplômes') }}</h2>
            <a href="{{ route('admin.templates.create') }}"
               class="inline-flex items-center px-4 py-2 bg-university-primary text-white text-sm font-semibold rounded-lg hover:bg-university-secondary transition-colors duration-200">
                + Nouveau modèle
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-flash />

        <div class="bg-white rounded-xl shadow-lg overflow-hidden p-6">
            @if ($templates->isEmpty())
                <p class="text-sm text-gray-500">Aucun modèle. Créez-en un pour permettre la génération de diplômes.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="py-3 pr-4 font-semibold">Nom</th>
                                <th class="py-3 pr-4 font-semibold">Niveau</th>
                                <th class="py-3 pr-4 font-semibold">Filière</th>
                                <th class="py-3 pr-4 font-semibold">Vue</th>
                                <th class="py-3 pr-4 font-semibold">Actif</th>
                                <th class="py-3 pr-4 font-semibold">Diplômes</th>
                                <th class="py-3 pr-4 font-semibold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($templates as $template)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                    <td class="py-3 pr-4 font-medium text-gray-900">{{ $template->name }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $template->level ?? '—' }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $template->field ?? '—' }}</td>
                                    <td class="py-3 pr-4 font-mono text-xs text-gray-600">{{ $template->view }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="px-2 py-0.5 rounded-full text-xs {{ $template->is_active ? 'bg-university-light text-university-primary' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $template->is_active ? 'Oui' : 'Non' }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $template->diplomas_count }}</td>
                                    <td class="py-3 pr-4 flex gap-3">
                                        <a href="{{ route('admin.templates.edit', $template) }}" class="text-university-primary hover:text-university-accent font-medium transition-colors">Modifier</a>
                                        <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                                              onsubmit="return confirm('Supprimer ce modèle ?');">
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

                <div class="mt-4">{{ $templates->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
