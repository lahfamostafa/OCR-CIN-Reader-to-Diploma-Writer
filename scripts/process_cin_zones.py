#!/usr/bin/env python3
"""
Script de traitement d'image CIN avec OpenCV pour Zonal OCR
Traite chaque zone (Prénom, Nom, Date de Naissance, Lieu de Naissance)
avec CLAHE et extrait le texte via Tesseract OCR
"""

import sys
import json
import argparse
import cv2
import pytesseract
from pathlib import Path

# Configuration CLAHE (Contrast Limited Adaptive Histogram Equalization)
CLAHE_CLIP_LIMIT = 2.0
CLAHE_TILE_SIZE = (8, 8)

# Configuration OCR
OCR_LANG = 'fra+eng'  # Français + Anglais pour CIN marocaine
OCR_CONFIG = r'--oem 3 --psm 6'  # OEM 3: LSTM, PSM 6: Assume single uniform block of text

def preprocess_image(image_path):
    """
    Prétraitement d'image avec CLAHE pour améliorer la lisibilité OCR
    
    Args:
        image_path: Chemin vers l'image d'entrée
        
    Returns:
        Chemin vers l'image prétraitée (temporaire)
    """
    # Charger l'image
    img = cv2.imread(str(image_path))
    if img is None:
        raise ValueError(f"Impossible de charger l'image: {image_path}")
    
    # Conversion en niveaux de gris
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    
    # Application CLAHE pour améliorer le contraste local
    clahe = cv2.createCLAHE(clipLimit=CLAHE_CLIP_LIMIT, tileGridSize=CLAHE_TILE_SIZE)
    enhanced_gray = clahe.apply(gray)
    
    # Lissage léger pour réduire le bruit
    denoised = cv2.medianBlur(enhanced_gray, 3)
    
    # Seuillage adaptatif pour améliorer la lisibilité du texte
    binary = cv2.adaptiveThreshold(
        denoised, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, 
        cv2.THRESH_BINARY, 11, 2
    )
    
    # Retour en BGR pour compatibilité
    final_img = cv2.cvtColor(binary, cv2.COLOR_GRAY2BGR)
    
    # Sauvegarder l'image prétraitée
    output_path = Path(image_path).parent / f"processed_{Path(image_path).name}"
    cv2.imwrite(str(output_path), final_img)
    
    return str(output_path)

def extract_text_from_zone(zone_path, zone_name):
    """
    Extrait le texte d'une zone spécifique après prétraitement
    
    Args:
        zone_path: Chemin vers l'image de la zone
        zone_name: Nom de la zone (pour logging)
        
    Returns:
        Texte extrait de la zone
    """
    try:
        # Prétraiter l'image
        processed_path = preprocess_image(zone_path)
        
        # Extraire le texte avec Tesseract OCR
        text = pytesseract.image_to_string(
            processed_path,
            lang=OCR_LANG,
            config=OCR_CONFIG
        )
        
        # Nettoyer le texte
        text = ' '.join(text.split())
        
        # Supprimer le fichier temporaire
        Path(processed_path).unlink(missing_ok=True)
        
        return text
        
    except Exception as e:
        print(f"Erreur lors de l'extraction de la zone {zone_name}: {e}", file=sys.stderr)
        return ""

def process_zones(zone_paths):
    """
    Traite toutes les zones OCR et retourne les résultats structurés
    
    Args:
        zone_paths: Dictionnaire des chemins de zones
            {
                'firstname': '/path/to/firstname.jpg',
                'lastname': '/path/to/lastname.jpg',
                'birthdate': '/path/to/birthdate.jpg',
                'birthplace': '/path/to/birthplace.jpg'
            }
        
    Returns:
        Dictionnaire des résultats OCR
    """
    results = {}
    
    # Traiter chaque zone
    for zone_name, zone_path in zone_paths.items():
        if zone_path and Path(zone_path).exists():
            text = extract_text_from_zone(zone_path, zone_name)
            results[zone_name] = text
        else:
            results[zone_name] = ""
    
    return results

def main():
    parser = argparse.ArgumentParser(description='Traitement Zonal OCR pour CIN marocaine')
    parser.add_argument('--firstname', required=True, help='Chemin vers l\'image de la zone Prénom')
    parser.add_argument('--lastname', required=True, help='Chemin vers l\'image de la zone Nom')
    parser.add_argument('--birthdate', required=True, help='Chemin vers l\'image de la zone Date de naissance')
    parser.add_argument('--birthplace', required=True, help='Chemin vers l\'image de la zone Lieu de naissance')
    
    args = parser.parse_args()
    
    # Construire le dictionnaire des zones
    zone_paths = {
        'firstname': args.firstname,
        'lastname': args.lastname,
        'birthdate': args.birthdate,
        'birthplace': args.birthplace,
    }
    
    # Traiter les zones
    results = process_zones(zone_paths)
    
    # Afficher les résultats en JSON pour Laravel
    print(json.dumps(results, ensure_ascii=False))
    
    return 0

if __name__ == '__main__':
    sys.exit(main())
