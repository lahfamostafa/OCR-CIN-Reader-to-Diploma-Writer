<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-bold text-xl text-university-primary leading-tight">
                {{ __('Registre Officiel des Diplômes Émis') }}
            </h2>
            <span class="text-xs bg-university-light text-university-primary font-bold px-3 py-1 rounded-full border border-university-primary/20">
                Al Akhawayn University Registry
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-university-light/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($diplomas->isEmpty())
                    <div class="p-12 text-center">
                        <div class="text-4xl mb-2">📜</div>
                        <p class="text-sm font-medium text-gray-400">Aucun diplôme n'a été numérisé ou généré pour le moment.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100 bg-gray-50/50">
                                    <th class="py-3.5 px-6 font-bold">Référence</th>
                                    <th class="py-3.5 px-6 font-bold">Lauréat (AUI Graduate)</th>
                                    <th class="py-3.5 px-6 font-bold">Modèle / Programme</th>
                                    <th class="py-3.5 px-6 font-bold">Généré par</th>
                                    <th class="py-3.5 px-6 font-bold">Date d'Émission</th>
                                    <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($diplomas as $diploma)
                                    <tr class="hover:bg-university-light/20 transition duration-150">
                                        <td class="py-4 px-6 font-mono text-xs text-university-primary font-bold">{{ $diploma->reference }}</td>
                                        <td class="py-4 px-6 font-semibold text-gray-900">{{ $diploma->student->full_name }}</td>
                                        <td class="py-4 px-6 text-gray-600 font-medium">{{ $diploma->template->name }}</td>
                                        <td class="py-4 px-6 text-xs text-gray-500 font-medium">{{ optional($diploma->generator)->name ?? '—' }}</td>
                                        <td class="py-4 px-6 text-gray-500">{{ $diploma->created_at->format('d/m/Y') }}</td>
                                        <td class="py-4 px-6 text-right flex items-center justify-end gap-3">
                                            <a href="{{ route('diplomas.show', $diploma) }}" class="text-xs font-bold text-university-primary bg-university-light px-3 py-1.5 rounded-lg hover:bg-university-primary hover:text-white transition">
                                                Consulter
                                            </a>
                                            <a href="{{ route('diplomas.download', $diploma) }}" class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">
                                                Fichier PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $diplomas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>