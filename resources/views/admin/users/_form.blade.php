<div>
    <x-input-label for="name" value="Nom" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                  :value="old('name', $user?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                  :value="old('email', $user?->email)" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="role" value="Rôle" />
    <select id="role" name="role" required
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="secretaire" @selected(old('role', $user?->role) === 'secretaire')>Secrétaire / Agent de saisie</option>
        <option value="admin" @selected(old('role', $user?->role) === 'admin')>Administrateur</option>
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" value="{{ $user ? 'Nouveau mot de passe (laisser vide pour conserver)' : 'Mot de passe' }}" />
    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                  :required="! $user" autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full"
                  :required="! $user" autocomplete="new-password" />
</div>
