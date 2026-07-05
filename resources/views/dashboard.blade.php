<x-app-layout>
    

    <div class="space-y-8 py-6">
        <x-flash />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $cards = [
                    ['label' => 'Lauréats AUI', 'value' => $stats['students'], 'icon' => '👨‍🎓', 'border' => 'border-university-primary'],
                    ['label' => 'Diplômes Validés', 'value' => $stats['diplomas'], 'icon' => '📜', 'border' => 'border-university-accent'],
                    ['label' => 'Modèles Actifs', 'value' => $stats['templates'], 'icon' => '⚙️', 'border' => 'border-gray-300'],
                    ['label' => 'Opérateurs', 'value' => $stats['users'], 'icon' => '💻', 'border' => 'border-university-primary'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="bg-white rounded-xl shadow-sm border-l-4 {{ $card['border'] }} p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $card['label'] }}</div>
                            <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $card['value'] }}</div>
                        </div>
                        <div class="text-3xl bg-gray-50 p-3 rounded-lg">{{ $card['icon'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-university-primary">Dernières Numérisations OCR</h3>
                    <p class="text-xs text-gray-500">Aperçu en temps réel des documents traités par l'administration.</p>
                </div>
                <a href="{{ route('cin.create') }}" class="px-4 py-2 bg-university-primary text-white text-xs font-bold rounded-lg hover:bg-university-dark transition">
                    + Nouveau Scan CIN
                </a>
            </div>

            @if ($recentDiplomas->isEmpty())
                <p class="text-sm text-gray-500 p-6 text-center">Aucun diplôme en attente ou généré récemment.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-400 uppercase text-xs tracking-wider border-b border-gray-200 bg-gray-50">
                                <th class="py-3 px-6 font-semibold">Référence</th>
                                <th class="py-3 px-6 font-semibold">Lauréat</th>
                                <th class="py-3 px-6 font-semibold">Modèle Académique</th>
                                <th class="py-3 px-6 font-semibold">Date de Création</th>
                                <th class="py-3 px-6 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentDiplomas as $diploma)
                                <tr class="hover:bg-university-light/40 transition-colors">
                                    <td class="py-4 px-6 font-mono text-xs text-university-primary font-bold">{{ $diploma->reference }}</td>
                                    <td class="py-4 px-6 font-semibold text-gray-900">{{ $diploma->student->full_name }}</td>
                                    <td class="py-4 px-6 text-gray-600 font-medium">{{ $diploma->template->name }}</td>
                                    <td class="py-4 px-6 text-gray-500">{{ $diploma->created_at->format('d/m/Y') }}</td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('diplomas.show', $diploma) }}" class="inline-flex items-center text-xs font-bold text-university-primary bg-university-light px-3 py-1.5 rounded-md hover:bg-university-primary hover:text-white transition">
                                            Consulter
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>