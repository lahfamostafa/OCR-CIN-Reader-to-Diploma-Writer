<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-university-primary leading-tight">{{ __('Registre Général des Lauréats') }}</h2>
            <a href="{{ route('cin.create') }}"
               class="inline-flex items-center px-4 py-2.5 bg-university-primary text-white text-xs font-bold rounded-lg hover:bg-university-secondary transition duration-200 shadow-sm">
                + Numériser une CIN (Nouveau)
            </a>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($students->isEmpty())
                    <div class="p-12 text-center text-gray-400 text-sm">
                        Aucun lauréat enregistré dans la base de données de l'AUI.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100 bg-gray-50/50">
                                    <th class="py-3.5 px-6 font-bold">Nom Complet</th>
                                    <th class="py-3.5 px-6 font-bold">N° CIN / ID Document</th>
                                    <th class="py-3.5 px-6 font-bold">Date de Naissance</th>
                                    <th class="py-3.5 px-6 font-bold">Diplômes Attribués</th>
                                    <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($students as $student)
                                    <tr class="hover:bg-university-light/20 transition-colors">
                                        <td class="py-4 px-6 font-semibold text-gray-900">{{ $student->full_name }}</td>
                                        <td class="py-4 px-6 font-mono text-xs font-bold text-university-primary">{{ $student->cin_number ?? '—' }}</td>
                                        <td class="py-4 px-6 text-gray-600">{{ optional($student->birth_date)->format('d/m/Y') ?? '—' }}</td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-university-light text-university-primary border border-university-primary/10">
                                                {{ $student->diplomas_count }} diplôme(s)
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right flex items-center justify-end gap-3">
                                            <a href="{{ route('students.show', $student) }}" class="text-xs font-bold text-university-primary hover:underline">Consulter</a>
                                            <a href="{{ route('students.edit', $student) }}" class="text-xs font-bold text-gray-500 hover:underline">Modifier</a>
                                            <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Supprimer définitivement ce lauréat ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs font-bold text-red-600 hover:underline">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>