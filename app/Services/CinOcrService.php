<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CinOcrService
{
    /**
     * Extraire les données d'une CIN (Recto + Verso) ou d'un Passeport via l'API Mistral AI (Vision)
     *
     * @param string|array $absolutePaths Chemin unique (Passeport) ou Tableau ['recto' => ..., 'verso' => ...]
     * @param string $documentType 'cin' ou 'passport'
     * @return array{first_name:?string,last_name:?string,birth_date:?string,birth_place:?string,cin_number:?string,passport_number:?string,address:?string}
     *
     * NOTE: Legacy methods (extractText, extractZonalText, parseFields, preprocess) have been removed.
     *       Tesseract OCR has been replaced by Mistral AI Vision API.
     */
    public function extractTextWithScaleway(string|array $absolutePaths, string $documentType = 'cin'): array
    {
        $fields = [
            'first_name' => null,
            'last_name' => null,
            'birth_date' => null,
            'birth_place' => null,
            'cin_number' => null,
            'passport_number' => null,
            'address' => null,
        ];

        try {
            // It is best practice to move your Scaleway secret key to your .env file
            $apiKey = env('SCALEWAY_API_KEY');
            if (empty($apiKey)) {
                Log::error("Clé API Scaleway absente.");
                return $fields;
            }

            // 1. Préparation dynamique des contenus d'images pour le prompt
            $imagesPayload = [];
            
            if ($documentType === 'cin' && is_array($absolutePaths)) {
                $promptText = "Analyse ces images de Carte d'Identité Nationale (CIN) marocaine (Recto et/ou Verso). Extrais les informations suivantes et retourne-les UNIQUEMENT sous la forme d'un objet JSON strict avec ces clés exactes : 'last_name' (le nom de famille en français), 'first_name' (le prénom en français), 'birth_date' (la date de naissance convertie au format YYYY-MM-DD), 'birth_place' (le lieu de naissance en français), 'cin_number' (le numéro de la carte comme AB123456), et 'address' (l'adresse complète présente au verso, s'il est fourni). Ne mets aucun texte ou markdown avant ou après le JSON.";
                
                foreach (['recto', 'verso'] as $side) {
                    if (isset($absolutePaths[$side]) && file_exists($absolutePaths[$side])) {
                        $mimeType = mime_content_type($absolutePaths[$side]);
                        $base64Image = base64_encode(file_get_contents($absolutePaths[$side]));
                        $imagesPayload[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64Image}"
                            ]
                        ];
                    }
                }
            } else {
                // Traitement Passeport
                $path = is_array($absolutePaths) ? ($absolutePaths['recto'] ?? '') : $absolutePaths;
                
                if (!file_exists($path)) {
                    Log::error("Fichier introuvable pour Scaleway OCR : {$path}");
                    return $fields;
                }

                $promptText = "Analyse cette image de Passeport. Extrais les informations suivantes et retourne-les UNIQUEMENT sous la forme d'un objet JSON strict avec ces clés exactes : 'last_name' (le nom de famille), 'first_name' (le prénom), 'birth_date' (la date de naissance au format YYYY-MM-DD), 'passport_number' (le numéro du passeport). Ne mets aucun texte ou markdown avant ou après le JSON.";
                
                $mimeType = mime_content_type($path);
                $base64Image = base64_encode(file_get_contents($path));
                $imagesPayload[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:{$mimeType};base64,{$base64Image}"
                    ]
                ];
            }

            // Combinaison du prompt textuel et des images payload
            $messagesContent = array_merge([
                [
                    'type' => 'text',
                    'text' => $promptText
                ]
            ], $imagesPayload);

            // 2. Appel à l'API de Scaleway (Compatible OpenAI format)
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->post('https://api.scaleway.ai/340bfa33-aa99-4176-9ee8-1481c5245186/v1/chat/completions', [
                    'model' => 'gemma-4-26b-a4b-it',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $messagesContent
                        ]
                    ],
                    'max_tokens' => 2048,
                    'temperature' => 1.0,
                    'top_p' => 0.95,
                    'presence_penalty' => 0,
                    'stream' => false,
                    // Si Gemma prend en charge le format JSON structuré directement, gardez 'json_object'.
                    // Sinon, utilisez 'text' comme dans votre script d'origine.
                    'response_format' => ['type' => 'text'] 
                ]);

            if ($response->failed()) {
                Log::error('Échec de l\'API Scaleway : ' . $response->body());
                return $fields;
            }

            // 3. Récupérer et décoder le JSON renvoyé
            $scalewayResult = $response->json('choices.0.message.content');
            
            // Nettoyage au cas où le modèle inclut des balises ```json ... ``` de démarquage
            $scalewayResult = preg_replace('/```json\s*|```/', '', $scalewayResult);
            $extractedData = json_decode(trim($scalewayResult), true);

            if ($extractedData) {
                $fields['first_name'] = $this->cleanName($extractedData['first_name'] ?? '');
                $fields['last_name'] = $this->cleanName($extractedData['last_name'] ?? '');
                $fields['birth_date'] = $extractedData['birth_date'] ?? null;
                $fields['birth_place'] = $this->cleanName($extractedData['birth_place'] ?? '');
                $fields['address'] = $extractedData['address'] ?? null;
                
                if (!empty($extractedData['cin_number'])) {
                    $fields['cin_number'] = strtoupper(str_replace(' ', '', $extractedData['cin_number']));
                }
                if (!empty($extractedData['passport_number'])) {
                    $fields['passport_number'] = strtoupper(str_replace(' ', '', $extractedData['passport_number']));
                }
            }

        } catch (\Throwable $e) {
            Log::error("Erreur durant l'OCR Scaleway: " . $e->getMessage());
        }

        return $fields;
    }

    /**
     * Parse birth date from OCR text (dd.mm.yyyy / dd/mm/yyyy / dd-mm-yyyy)
     */

    /**
     * Optional image preprocessing (grayscale + contrast) via GD to improve OCR.
     * Returns the path of a temporary processed image, or the original path on failure.
     */
    public function preprocess(string $absolutePath): string
    {
        if (! extension_loaded('gd')) {
            return $absolutePath;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return $absolutePath;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => @imagecreatefrompng($absolutePath),
            IMAGETYPE_GIF => @imagecreatefromgif($absolutePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : false,
            default => false,
        };

        if ($image === false || $image === null) {
            return $absolutePath;
        }

        imagefilter($image, IMG_FILTER_GRAYSCALE);
        imagefilter($image, IMG_FILTER_CONTRAST, -20);

        $tmp = tempnam(sys_get_temp_dir(), 'cin_').'.png';
        imagepng($image, $tmp);
        imagedestroy($image);

        return $tmp;
    }

    /**
     * Parse the raw OCR text into structured CIN fields.
     *
     * @return array{first_name:?string,last_name:?string,birth_date:?string,birth_place:?string,cin_number:?string}
     */
    public function parseFields(string $text): array
    {
        $fields = [
            'first_name' => null,
            'last_name' => null,
            'birth_date' => null,
            'birth_place' => null,
            'cin_number' => null,
        ];

        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));

        // CIN number: Moroccan CINs are usually 1-2 letters followed by 5-7 digits.
        if (preg_match('/\b([A-Z]{1,2}\s?\d{4,7})\b/u', $text, $m)) {
            $fields['cin_number'] = strtoupper(str_replace(' ', '', $m[1]));
        }

        // Birth date: dd.mm.yyyy / dd/mm/yyyy / dd-mm-yyyy
        if (preg_match('/\b(\d{2})[.\/\-](\d{2})[.\/\-](\d{4})\b/', $text, $m)) {
            $fields['birth_date'] = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        foreach ($lines as $i => $line) {
            $upper = mb_strtoupper($line);

            // Birth place markers (FR/EN)
            if (preg_match('/(N(É|E)\s*(À|A)|BORN IN|LIEU)/u', $upper)) {
                $candidate = trim(preg_replace('/.*(N(É|E)\s*(À|A)|BORN IN|LIEU)\s*:?/u', '', $line));
                if ($candidate !== '') {
                    $fields['birth_place'] = $this->cleanName($candidate);
                }
            }
        }

        // Heuristic for names: take the two longest uppercase alpha lines that look like names.
        $nameCandidates = [];
        foreach ($lines as $line) {
            $clean = $this->cleanName($line);
            if ($clean === '') {
                continue;
            }
            // a name line is mostly letters and spaces, 2+ chars, no digits
            if (preg_match('/^[\p{L}\s\'\-]{2,}$/u', $clean) && ! preg_match('/(ROYAUME|MAROC|CARTE|NATIONALE|IDENTIT|KINGDOM|MOROCCO|VALABLE|VALID)/iu', $clean)) {
                $nameCandidates[] = $clean;
            }
        }

        if (isset($nameCandidates[0])) {
            $fields['last_name'] = $nameCandidates[0];
        }
        if (isset($nameCandidates[1])) {
            $fields['first_name'] = $nameCandidates[1];
        }

        return $fields;
    }

    private function cleanName(string $value): string
    {
        $value = preg_replace('/[^\p{L}\s\'\-]/u', ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }
}
