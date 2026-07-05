<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\CinOcrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class CinUploadController extends Controller
{
    public function __construct(private CinOcrService $ocr) {}

    public function create(): View
    {
        return view('cin.upload');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validation stricte selon le type de document sélectionné
        $request->validate([
            'document_type'  => ['required', 'in:cin,passport'],
            'document_recto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'document_verso' => ['required_if:document_type,cin', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $docType = $request->input('document_type');
        $fields = [];
        
        // Tableaux pour garder trace des fichiers sauvegardés en cas de rollback nécessaire
        $storedPaths = []; 
        $mainPath = '';

        try {
            // 2. Traitement spécifique : Mode CIN (Recto + Verso)
            if ($docType === 'cin') {
                $rectoPath = $request->file('document_recto')->store('cin', 'public');
                $storedPaths[] = $rectoPath;
                $mainPath = $rectoPath;

                $absolutePaths = [
                    'recto' => Storage::disk('public')->path($rectoPath),
                ];

                if ($request->hasFile('document_verso')) {
                    $versoPath = $request->file('document_verso')->store('cin', 'public');
                    $storedPaths[] = $versoPath;
                    $absolutePaths['verso'] = Storage::disk('public')->path($versoPath);
                }

                // Appel de l'OCR Mistral Multimodal
                $fields = $this->ocr->extractTextWithScaleway($absolutePaths, 'cin');

            // 3. Traitement spécifique : Mode Passeport (Capture unique)
            } else {
                $mainPath = $request->file('document_recto')->store('passport', 'public');
                $storedPaths[] = $mainPath;
                
                $absolutePath = Storage::disk('public')->path($mainPath);

                // Appel de l'OCR Mistral pour Passeport
                $fields = $this->ocr->extractTextWithScaleway($absolutePath, 'passport');
            }

            // Encodage propre du retour JSON de l'OCR
            $rawText = json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // 4. Création de l'enregistrement de l'étudiant avec l'adresse incluse !
            $student = Student::create([
                'first_name'     => $fields['first_name'] ?? '',
                'last_name'      => $fields['last_name'] ?? '',
                'birth_date'     => $fields['birth_date'] ?? null,
                'birth_place'    => $fields['birth_place'] ?? null,
                'address'        => $fields['address'] ?? null, // <- Nouvelle donnée sauvegardée !
                'cin_number'     => ($docType === 'cin') ? ($fields['cin_number'] ?? null) : ($fields['passport_number'] ?? null),
                'cin_image_path' => $mainPath,
                'ocr_raw_text'   => $rawText,
                'created_by'     => $request->user()->id,
            ]);

            return redirect()
                ->route('students.edit', $student)
                ->with('status', 'Analyse automatique terminée. Veuillez valider et compléter les informations.');

        } catch (\Throwable $e) {
            // En cas de crash (ex: API Mistral inaccessible), on supprime les fichiers orphelins
            Log::error("Échec du traitement OCR dans le contrôleur : " . $e->getMessage());

            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['document_recto' => "Une erreur est survenue lors de l'analyse automatique de votre document."]);
        }
    }
}