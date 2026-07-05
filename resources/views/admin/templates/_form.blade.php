<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Intitulé Global du Modèle Académique" class="text-xs font-bold text-gray-600" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('name', $template?->name)" required placeholder="Ex: Bachelor of Science in Computer Science" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="level" value="Niveau du Grade" class="text-xs font-bold text-gray-600" />
            <x-text-input id="level" name="level" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('level', $template?->level)" placeholder="Ex: Bachelor / Master / PhD" />
            <x-input-error :messages="$errors->get('level')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="field" value="Discipline / Majeure (Field)" class="text-xs font-bold text-gray-600" />
            <x-text-input id="field" name="field" type="text" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary" :value="old('field', $template?->field)" placeholder="Ex: Computer Science" />
            <x-input-error :messages="$errors->get('field')" class="mt-1" />
        </div>
    </div>

    <div>
        <x-input-label for="view" value="Fichier Structure HTML Associé (Vue PDF)" class="text-xs font-bold text-gray-600" />
        <select id="view" name="view" required class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary rounded-lg text-sm shadow-sm">
            @foreach ($views as $view)
                <option value="{{ $view }}" @selected(old('view', $template?->view) === $view)>{{ $view }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('view')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="description" value="Description / Notes Administratives (Optionnel)" class="text-xs font-bold text-gray-600" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-200 focus:border-university-primary focus:ring-university-primary rounded-lg text-sm shadow-sm" placeholder="Ajoutez des notes internes concernant les critères de ce modèle...">{{ old('description', $template?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div class="pt-2">
        <label class="inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template?->is_active ?? true)) class="rounded border-gray-300 text-university-primary focus:ring-university-primary shadow-sm">
            <span class="ms-2 text-xs font-bold text-gray-600 uppercase tracking-wide">Rendre ce modèle actif immédiatement</span>
        </label>
    </div>
</div>