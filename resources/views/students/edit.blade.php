<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-university-primary leading-tight">
            {{ __('Validation & Authentification des Données Émanant de la CIN') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-university-light/20">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-md font-bold text-university-primary mb-3 uppercase tracking-wider">Document d'identité d'Origine</h3>
                        @if ($student->cin_image_path)
                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-inner bg-gray-50 p-2">
                                <img src="{{ Storage::url($student->cin_image_path) }}" alt="CIN" class="w-full h-auto rounded-lg">
                            </div>
                        @else
                            <div class="py-20 text-center text-sm text-gray-400 bg-gray-50 rounded-xl border border-dashed">Aucune capture d'identité disponible.</div>
                        @endif
                    </div>

                    @if ($student->ocr_raw_text)
                        <details class="mt-6 group">
                            <summary class="text-xs font-bold text-gray-500 cursor-pointer hover:text-university-primary transition flex items-center gap-1 select-none">
                                <span>📋 Afficher les métadonnées textuelles brutes (OCR)</span>
                            </summary>
                            <pre class="mt-2 text-xs bg-university-light p-4 rounded-xl border border-university-primary/10 overflow-x-auto text-university-primary font-mono whitespace-pre-wrap">{{ $student->ocr_raw_text }}</pre>
                        </details>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-4 border-b border-gray-100 pb-3">
                        <h3 class="text-md font-bold text-gray-800">Données à approuver</h3>
                        <p class="text-xs text-gray-500">Veuillez vérifier l'exactitude des informations orthographiques avant la génération finale.</p>
                    </div>

                    <form method="POST" action="{{ route('students.update', $student) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name" value="Prénom / First Name" class="text-xs font-bold text-gray-600" />
                                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('first_name', $student->first_name)" required />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="last_name" value="Nom / Last Name" class="text-xs font-bold text-gray-600" />
                                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('last_name', $student->last_name)" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="birth_date" value="Date de naissance" class="text-xs font-bold text-gray-600" />
                                <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('birth_date', \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="birth_place" value="Lieu de naissance" class="text-xs font-bold text-gray-600" />
                                <x-text-input id="birth_place" name="birth_place" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('birth_place', $student->birth_place)" />
                                <x-input-error :messages="$errors->get('birth_place')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="address" value="Adresse de Résidence" class="text-xs font-bold text-gray-600" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('address', $student->address)" />
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="cin_number" value="Code National d'Identité (CIN)" class="text-xs font-bold text-gray-600" />
                            <x-text-input id="cin_number" name="cin_number" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary font-mono font-bold text-university-primary" :value="old('cin_number', $student->cin_number)" />
                            <x-input-error :messages="$errors->get('cin_number')" class="mt-1" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ route('cin.create') }}" class="text-xs font-bold text-gray-400 hover:text-red-600 transition-colors">Recommencer le scan</a>
                            <button type="submit" class="px-5 py-2.5 bg-university-primary text-white text-xs font-bold rounded-lg shadow hover:bg-university-dark transition">
                                Valider et Structurer le Diplôme
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>