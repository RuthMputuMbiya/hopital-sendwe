<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat d'examen - Hôpital Sendwe</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .document {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1a4d8c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a4d8c;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        .title {
            text-align: center;
            margin-bottom: 30px;
        }
        .title h2 {
            color: #1a4d8c;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .info-box {
            flex: 1;
        }
        .info-box p {
            margin: 5px 0;
        }
        .resultat-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #1a4d8c;
        }
        .resultat-box h3 {
            color: #1a4d8c;
            margin-bottom: 15px;
        }
        .resultat-box p {
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #999;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .btn-print {
            background: #1a4d8c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #0e3570;
        }
    </style>
</head>
<body>
    <div class="document">
        <!-- En-tête de l'hôpital -->
        <div class="header">
            <h1>HÔPITAL SENDWE</h1>
            <p>Lubumbashi - République Démocratique du Congo</p>
            <p>Laboratoire d'analyses médicales</p>
        </div>

        <!-- Titre -->
        <div class="title">
            <h2>RÉSULTAT D'EXAMEN MÉDICAL</h2>
        </div>

        <!-- Informations -->
        <div class="info-grid">
            <div class="info-box">
                <p><strong>N° Examen:</strong> {{ $examen->numero_examen }}</p>
                <p><strong>Date de l'examen:</strong> {{ \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') }}</p>
                <p><strong>Heure:</strong> {{ $examen->heure_examen }}</p>
            </div>
            <div class="info-box">
                <p><strong>Type d'examen:</strong> {{ $examen->type_examen }}</p>
                <p><strong>Laborantin:</strong> {{ $examen->laborantin->nom_complet ?? 'N/A' }}</p>
                <p><strong>Statut:</strong> Terminé</p>
            </div>
        </div>

        <!-- Informations patient -->
        <div class="info-grid">
            <div class="info-box">
                <p><strong>Patient:</strong> {{ $examen->patient->nom_complet ?? 'N/A' }}</p>
                <p><strong>N° Fiche:</strong> {{ $examen->patient->numero_fiche ?? 'N/A' }}</p>
            </div>
            <div class="info-box">
                <p><strong>Date naissance:</strong> {{ \Carbon\Carbon::parse($examen->patient->date_naissance)->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Sexe:</strong> {{ $examen->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
            </div>
        </div>

        <!-- Résultat -->
        <div class="resultat-box">
            <h3><i class="fas fa-chart-line"></i> RÉSULTAT DE L'ANALYSE</h3>
            <div style="white-space: pre-wrap;">{{ $examen->resultat }}</div>
        </div>

        <!-- Maladie associée -->
        @if($examen->maladie)
        <div class="resultat-box" style="border-left-color: #f39c12;">
            <h3><i class="fas fa-disease"></i> MALADIE ASSOCIÉE</h3>
            <p><strong>{{ $examen->maladie->nom_maladie }}</strong></p>
            <p>{{ $examen->maladie->description }}</p>
        </div>
        @endif

        <!-- Observations -->
        <div class="resultat-box" style="border-left-color: #27ae60;">
            <h3><i class="fas fa-comment-dots"></i> OBSERVATIONS</h3>
            <p>_________________________________________________________________________________</p>
            <p>_________________________________________________________________________________</p>
            <p>_________________________________________________________________________________</p>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line">Signature du Médecin</div>
            </div>
            <div class="signature">
                <div class="signature-line">Cachet de l'Hôpital</div>
            </div>
            <div class="signature">
                <div class="signature-line">Signature du Patient</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Ce document est un résultat officiel de l'Hôpital Sendwe</p>
            <p>Lubumbashi, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
            <p>Document généré automatiquement - Tous droits réservés</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Imprimer le résultat
        </button>
        <button onclick="window.close()" class="btn-print" style="background: #666; margin-left: 10px;">
            <i class="fas fa-times"></i> Fermer
        </button>
    </div>

    <script>
        // Impression automatique (optionnel)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>