<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaTemplate;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DiplomaController extends Controller
{
    public function index(): View
    {
        $diplomas = Diploma::with(['student', 'template', 'generator'])
            ->latest()
            ->paginate(15);

        return view('diplomas.index', compact('diplomas'));
    }

    /**
     * Generate a diploma PDF for a student from a selected template.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'diploma_template_id' => ['required', 'exists:diploma_templates,id'],
            'mention' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:255'],
        ]);

        $student = Student::findOrFail($data['student_id']);
        $template = DiplomaTemplate::findOrFail($data['diploma_template_id']);

        $diploma = Diploma::create([
            'student_id' => $student->id,
            'diploma_template_id' => $template->id,
            'generated_by' => $request->user()->id,
            'reference' => $this->generateReference(),
            'mention' => $data['mention'] ?? null,
            'academic_year' => $data['academic_year'] ?? null,
        ]);

        $this->renderPdf($diploma);

        return redirect()
            ->route('diplomas.show', $diploma)
            ->with('status', 'Diplôme généré avec succès.');
    }

    public function show(Diploma $diploma): View
    {
        $diploma->load(['student', 'template', 'generator']);

        return view('diplomas.show', compact('diploma'));
    }

    /**
     * Stream the generated PDF inline for preview.
     */
    public function preview(Diploma $diploma): Response
    {
        $diploma->load(['student', 'template']);
        $pdf = $this->buildPdf($diploma);

        return $pdf->stream($this->filename($diploma));
    }

    /**
     * Download the stored PDF (regenerating if missing).
     */
    public function download(Diploma $diploma): BinaryFileResponse
    {
        if (! $diploma->pdf_path || ! Storage::disk('public')->exists($diploma->pdf_path)) {
            $this->renderPdf($diploma);
            $diploma->refresh();
        }

        return response()->download(
            Storage::disk('public')->path($diploma->pdf_path),
            $this->filename($diploma)
        );
    }

    public function destroy(Diploma $diploma): RedirectResponse
    {
        if ($diploma->pdf_path) {
            Storage::disk('public')->delete($diploma->pdf_path);
        }

        $diploma->delete();

        return redirect()
            ->route('diplomas.index')
            ->with('status', 'Diplôme supprimé.');
    }

    private function buildPdf(Diploma $diploma)
    {
        $view = "diplomas.templates.{$diploma->template->view}";

        if (! view()->exists($view)) {
            $view = 'diplomas.templates.default';
        }

        return Pdf::loadView($view, [
            'diploma' => $diploma,
            'student' => $diploma->student,
            'template' => $diploma->template,
        ])->setPaper('a4', 'landscape');
    }

    private function renderPdf(Diploma $diploma): void
    {
        $diploma->loadMissing(['student', 'template']);
        $pdf = $this->buildPdf($diploma);

        $path = "diplomas/{$diploma->reference}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $diploma->update(['pdf_path' => $path]);
    }

    private function filename(Diploma $diploma): string
    {
        $name = Str::slug("{$diploma->student->last_name}-{$diploma->student->first_name}-{$diploma->reference}");

        return "diplome-{$name}.pdf";
    }

    private function generateReference(): string
    {
        return 'DIP-'.now()->format('Y').'-'.strtoupper(Str::random(8));
    }
}
