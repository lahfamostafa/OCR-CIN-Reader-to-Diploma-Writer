<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('diplomas.index') }}" class="text-gray-400 hover:text-university-primary transition">&larr;</a>
            <h2 class="font-bold text-xl text-university-primary leading-tight">
                {{ __('Détails du Diplôme Émis') }} — <span class="font-mono text-lg text-gray-500">{{ $diploma->reference }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-university-light/20 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6 border-b border-gray-100 pb-4">
                    <div class="space-y-1">
                        <div class="text-lg font-bold text-gray-900">
                            {{ $diploma->student->full_name }}
                        </div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                            {{ $diploma->template->name }} @if ($diploma->mention) — Mention {{ $diploma->mention }} @endif
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('diplomas.preview', $diploma) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-100 transition shadow-sm">
                            🖥️ Plein écran / Imprimer
                        </a>
                        <a href="{{ route('diplomas.download', $diploma) }}"
                           class="inline-flex items-center px-4 py-2 bg-university-primary text-white text-xs font-bold rounded-lg hover:bg-university-dark transition shadow-md">
                            📥 Télécharger l'original PDF
                        </a>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-inner bg-gray-50 p-2" style="height: 78vh;">
                    <iframe src="{{ route('diplomas.preview', $diploma) }}" class="w-full h-full rounded-lg bg-white" title="AUI Diploma Live View"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>