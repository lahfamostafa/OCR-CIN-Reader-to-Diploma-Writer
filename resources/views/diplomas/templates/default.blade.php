<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Serif', serif; color: #0A3B21; background: #ffffff; }
        .sheet {
            width: 100%;
            height: 540px;
            border: 10px solid #0F5832; /* Bordure principale Vert Al Akhawayn */
            padding: 24px;
            position: relative;
        }
        .inner-border {
            border: 2px solid #C8A24A; /* Liseré Or Institutionnel */
            height: 100%;
            padding: 25px 45px;
            text-align: center;
            position: relative;
        }
        .logo-header {
            position: absolute;
            top: 20px;
            left: 35px;
            height: 48px;
            opacity: 0.9;
        }
        .kingdom { font-size: 12px; letter-spacing: 1.5px; color: #4b5563; font-weight: bold; text-transform: uppercase; }
        .university { font-size: 18px; font-weight: bold; margin-top: 6px; color: #0F5832; letter-spacing: 1px; }
        .title {
            font-size: 38px;
            font-weight: bold;
            color: #0F5832;
            margin-top: 25px;
            letter-spacing: 3px;
        }
        .subtitle { font-size: 15px; color: #C8A24A; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .body-text { font-size: 15px; margin-top: 28px; line-height: 1.9; color: #1f2937; }
        .name {
            font-size: 23px;
            font-weight: bold;
            color: #0A3B21;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .level { font-size: 19px; font-weight: bold; color: #0F5832; margin-top: 8px; }
        .mention { font-size: 14px; font-style: italic; margin-top: 4px; color: #374151; }
        
        /* Sceau officiel de l'université */
        .seal { 
            position: absolute;
            bottom: 65px;
            left: 50%;
            transform: translateX(-50%);
            width: 75px;
            height: 75px;
            border: 2px dashed #C8A24A;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: #C8A24A;
            letter-spacing: 1px;
            background: #F4F9F6;
        }

        .footer {
            position: absolute;
            bottom: 25px;
            left: 45px;
            right: 45px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #4b5563;
            border-top: 1px solid #E5E7EB;
            padding-top: 12px;
        }
        .col { flex: 1; text-align: center; }
        .col:first-child { text-align: left; }
        .col:last-child { text-align: right; font-weight: bold; color: #0F5832; }
        .ref { position: absolute; top: 10px; right: 15px; font-family: monospace; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="inner-border">
            <img src="{{ public_path('images/logo-university.webp') }}" class="logo-header" alt="Logo Al Akhawayn">
            
            <div class="ref">Réf : {{ $diploma->reference }}</div>
            <div class="kingdom">Royaume du Maroc</div>
            <div class="university">AL AKHAWAYN UNIVERSITY IN IFRANE</div>

            <div class="title">DIPLÔME ACADÉMIQUE</div>
            <div class="subtitle">{{ $template->name }}</div>

            <div class="body-text">
                Par décision du conseil académique de l'université, le présent titre est décerné à :
                <div class="name">{{ $student->first_name }} {{ $student->last_name }}</div>
                @if ($student->birth_date)
                    né(e) le {{ \Illuminate\Support\\Carbon::parse($student->birth_date)->format('d/m/Y') }}@if($student->birth_place) à {{ $student->birth_place }}@endif
                @endif
                @if ($student->cin_number)
                    <br>Titulaire de la CIN n° : <strong style="font-family: monospace;">{{ $student->cin_number }}</strong>
                @endif

                @if ($template->level)
                    <div class="level">{{ $template->level }}@if($template->field) — {{ $template->field }}@endif</div>
                @endif
                @if ($diploma->mention)
                    <div class="mention">Mention Officielle : <strong style="color: #0F5832;">{{ $diploma->mention }}</strong></div>
                @endif
            </div>

            <div class="seal">AUI SEAL</div>

            <div class="footer">
                <span class="col">
                    @if ($diploma->academic_year) Année Universitaire : {{ $diploma->academic_year }} @endif
                </span>
                <span class="col">Fait à Ifrane, le {{ $diploma->created_at->format('d/m/Y') }}</span>
                <span class="col">La Présidence de l'Université</span>
            </div>
        </div>
    </div>
</body>
</html>