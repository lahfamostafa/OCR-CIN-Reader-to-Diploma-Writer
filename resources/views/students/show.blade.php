<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lauréat') }} — {{ $student->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 lg:col-span-1">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-gray-500">Nom</dt><dd class="font-medium">{{ $student->last_name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Prénom</dt><dd class="font-medium">{{ $student->first_name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Date de naissance</dt><dd class="font-medium">{{ optional($student->birth_date)->format('d/m/Y') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Lieu de naissance</dt><dd class="font-medium">{{ $student->birth_place ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">N° CIN</dt><dd class="font-medium">{{ $student->cin_number ?? '—' }}</dd></div>
                    </dl>

                    @if ($student->cin_image_path)
                        <img src="{{ Storage::url($student->cin_image_path) }}" alt="CIN" class="mt-4 w-full rounded border">
                    @endif

                    <a href="{{ route('students.edit', $student) }}"
                       class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Modifier les informations</a>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Générer un diplôme</h3>

                    <form method="POST" action="{{ route('diplomas.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div>
                            <x-input-label for="diploma_template_id" value="Modèle de diplôme" />
                            <select id="diploma_template_id" name="diploma_template_id" required
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">— Sélectionner —</option>
                                @forelse (\App\Models\DiplomaTemplate::where('is_active', true)->orderBy('name')->get() as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}@if($template->level) ({{ $template->level }})@endif</option>
                                @empty
                                @endforelse
                            </select>
                            <x-input-error :messages="$errors->get('diploma_template_id')" class="mt-2" />
                            @if (\App\Models\DiplomaTemplate::where('is_active', true)->count() === 0)
                                <p class="mt-2 text-sm text-amber-600">Aucun modèle actif. Demandez à un administrateur d'en créer un.</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="mention" value="Mention (optionnel)" />
                                <x-text-input id="mention" name="mention" type="text" class="mt-1 block w-full"
                                              :value="old('mention')" placeholder="Très bien" />
                            </div>
                            <div>
                                <x-input-label for="academic_year" value="Année universitaire (optionnel)" />
                                <x-text-input id="academic_year" name="academic_year" type="text" class="mt-1 block w-full"
                                              :value="old('academic_year', (now()->year - 1).'-'.now()->year)" />
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-primary-button>Générer le diplôme (PDF)</x-primary-button>
                        </div>
                    </form>

                    <hr class="my-6">

                    <h4 class="font-semibold text-gray-800 mb-3">Diplômes de ce lauréat</h4>
                    @if ($student->diplomas->isEmpty())
                        <p class="text-sm text-gray-500">Aucun diplôme généré.</p>
                    @else
                        <ul class="divide-y">
                            @foreach ($student->diplomas as $diploma)
                                <li class="py-2 flex items-center justify-between text-sm">
                                    <div>
                                        <span class="font-mono text-xs">{{ $diploma->reference }}</span> —
                                        {{ $diploma->template->name }}
                                        <span class="text-gray-400">({{ $diploma->created_at->format('d/m/Y') }})</span>
                                    </div>
                                    <a href="{{ route('diplomas.show', $diploma) }}" class="text-indigo-600 hover:underline">Voir</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
