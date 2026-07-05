<?php

namespace App\Http\Controllers;

use App\Models\DiplomaTemplate;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::withCount('diplomas')->latest()->paginate(15);

        return view('students.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load(['diplomas.template', 'creator']);

        return view('students.show', compact('student'));
    }

    /**
     * Validation / mapping screen: CIN image side-by-side with the OCR form.
     */
    public function edit(Student $student): View
    {
        $templates = DiplomaTemplate::where('is_active', true)->orderBy('name')->get();

        return view('students.edit', compact('student', 'templates'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'cin_number' => ['nullable', 'string', 'max:255'],
        ]);

        $student->update($data);

        return redirect()
            ->route('students.show', $student)
            ->with('status', 'Informations du lauréat enregistrées.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->cin_image_path) {
            Storage::disk('public')->delete($student->cin_image_path);
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('status', 'Lauréat supprimé.');
    }
}
