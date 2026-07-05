<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiplomaTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiplomaTemplateController extends Controller
{
    public function index(): View
    {
        $templates = DiplomaTemplate::withCount('diplomas')->orderBy('name')->paginate(15);

        return view('admin.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.templates.create', ['views' => $this->availableViews()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        DiplomaTemplate::create($data);

        return redirect()
            ->route('admin.templates.index')
            ->with('status', 'Modèle de diplôme créé.');
    }

    public function edit(DiplomaTemplate $template): View
    {
        return view('admin.templates.edit', [
            'template' => $template,
            'views' => $this->availableViews(),
        ]);
    }

    public function update(Request $request, DiplomaTemplate $template): RedirectResponse
    {
        $data = $this->validateData($request);
        $template->update($data);

        return redirect()
            ->route('admin.templates.index')
            ->with('status', 'Modèle mis à jour.');
    }

    public function destroy(DiplomaTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('status', 'Modèle supprimé.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'field' => ['nullable', 'string', 'max:255'],
            'view' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /**
     * Available blade template files under resources/views/diplomas/templates.
     *
     * @return array<int, string>
     */
    private function availableViews(): array
    {
        $dir = resource_path('views/diplomas/templates');

        if (! is_dir($dir)) {
            return ['default'];
        }

        return collect(glob($dir.'/*.blade.php'))
            ->map(fn ($path) => basename($path, '.blade.php'))
            ->values()
            ->all();
    }
}
