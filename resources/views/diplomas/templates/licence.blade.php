<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Serif', serif; color: #0A3B21; }
        .sheet {
            width: 100%;
            height: 540px;
            border: 12px double #0F5832; /* Ligne double officielle Vert AUI */
            padding: 36px 60px;
            text-align: center;
            position: relative;
            background: #ffffff;
        }
        .logo-background {
            position: absolute;
            top: 40px;
            left: 50px;
            height: 50px;
            opacity: 0.8;
        }
        .kingdom { font-size: 13px; letter-spacing: 1.5px; color: #374151; font-weight: bold; }
        .university { font-size: 18px; font-weight: bold; margin-top: 6px; color: #0F5832; letter-spacing: 1px; }
        .title {
            font-size: 36px;
            font-weight: bold;
            color: #0F5832;
            margin-top: 35px;
            letter-spacing: 3px;
        }
        .subtitle { font-size: 16px; color: #C8A24A; margin-top: 6px; font-style: italic; font-weight: bold; }
        .body-text { font-size: 16px; margin-top: 30px; line-height: 2; color: #1f2937; }
        .name {
            font-size: 24px;
            font-weight: bold;
            color: #0A3B21;
            margin: 12px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .level { font-size: 20px; font-weight: bold; color: #0F5832; margin-top: 8px; }
        .mention { font-size: 15px; font-style: italic; margin-top: 6px; color: #4b5563; }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #4b5563;
            border-top: 1px solid #E5E7EB;
            padding-top: 15px;
        }
        .col { flex: 1; text-align: center; }
        .col:first-child { text-align: left; }
        .col:last-child { text-align: right; font-weight: bold; color: #0F5832; }
        .ref { position: absolute; top: 15px; right: 20px; font-family: monospace; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="sheet">
        <img src="{{ public_path('images/logo-university.webp') }}" class="logo-background" alt="Logo AUI">
        
        <div class="ref">Réf : {{ $diploma->reference }}</div>
        <div class="kingdom">ROYAUME DU MAROC — MINISTÈRE DE L'ENSEIGNEMENT SUPÉRIEUR</div>
        <div class="university">AL AKHAWAYN UNIVERSITY IN IFRANE</div>

        <div class="title">DIPLÔME DE LICENCE</div>
        <div class="subtitle">GRADUATION CERTIFICATE</div>

        <div class="body-text">
            Vu les procès-verbaux des jurys d'examen, il est attesté que :
            <div class="name">{{ $student->first_name }} {{ $student->last_name }}</div>
            @if ($student->birth_date)
    né(e) le {{ \Illuminate\Support\Carbon::parse($student->birth_date)->format('d/m/Y') }} @if($student->birth_place) à {{ $student->birth_place }}@endif
@endif
            <br>a satisfait de manière rigoureuse aux exigences académiques et obtenu le grade universitaire de :
            @if ($template->level)
                <div class="level">{{ $template->level }} @if($template->field) en {{ $template->field }}@endif</div>
            @endif
            @if ($diploma->mention)
                <div class="mention">avec la mention officielle <strong style="color: #0F5832;">« {{ $diploma->mention }} »</strong></div>
            @endif
        </div>

        <div class="footer">
            <span class="col">@if ($diploma->academic_year) Année Universitaire : <strong>{{ $diploma->academic_year }}</strong> @endif</span>
            <span class="col">Fait à Ifrane, le {{ $diploma->created_at->format('d/m/Y') }}</span>
            <span class="col">Le Président de l'Université</span>
        </div>
    </div>
</body>
</html>