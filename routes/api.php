<?php

use App\Services\CinOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/process-document', function (Request $request, CinOcrService $ocrService) {
    
    // 1. Valider que le Front-end envoie bien l'image
    $request->validate([
        'image' => 'required|string', // Chaîne Base64 de la caméra
    ]);

    try {
        $base64Image = $request->input('image');

        // Nettoyer la chaîne Base64 du préfixe data:image/...
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $extension = strtolower($type[1]);
        } else {
            return response()->json(['error' => 'Format d\'image malformé'], 422);
        }

        $imageBytes = base64_decode($base64Image);

        // 2. Créer un fichier temporaire sur le serveur
        $fileName = 'scan_' . time() . '_' . Str::random(5) . '.' . $extension;
        $tempDir = storage_path('app/public/temp_scans');
        
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $absolutePath = $tempDir . '/' . $fileName;
        file_put_contents($absolutePath, $imageBytes);

        // 3. APPELER LE SERVICE MISTRAL !
        $data = $ocrService->extractTextWithMistral($absolutePath);

        // 4. Supprimer le fichier temporaire après lecture pour ne pas surcharger le serveur
        if (file_exists($absolutePath)) {
            @unlink($absolutePath);
        }

        // 5. Renvoyer la réponse structurée que ton Front-end attend (pour éliminer l'erreur de log)
        return response()->json([
            'success' => true,
            'message' => 'Analyse Mistral complétée',
            'extracted_data' => [
                'nom' => $data['last_name'] ?? 'Inconnu',
                'prenom' => $data['first_name'] ?? 'Inconnu',
                'cin' => $data['cin_number'] ?? 'Inconnu',
                'date_naissance' => $data['birth_date'] ?? 'Inconnu',
                'lieu_naissance' => $data['birth_place'] ?? 'Inconnu',
            ]
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Erreur Route API OCR : ' . $e->getMessage());
        return response()->json(['error' => 'Erreur interne du serveur'], 500);
    }
});