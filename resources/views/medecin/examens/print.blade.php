<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat d'examen - {{ $examen->numero_examen }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
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
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #666;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .badge-print {
            display: inline-block;
            background: #1a4d8c;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            margin-top: 8px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            color: #1a4d8c;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .info-item {
            padding: 5px 0;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            width: 140px;
            display: inline-block;
            font-size: 13px;
        }
        .info-value {
            font-weight: 500;
            font-size: 13px;
        }
        .resultat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 10px;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 11px;
            color: #999;
        }
        .footer .hospital-info {
            color: #1a4d8c;
            font-weight: 600;
        }
        .print-btn-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .print-btn {
            padding: 12px 30px;
            background: #1a4d8c;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .print-btn:hover {
            background: #0e3570;
            transform: translateY(-2px);
        }
        .close-btn {
            padding: 12px 30px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 10px;
        }
        .close-btn:hover {
            background: #5a6268;
        }
        .copy-btn {
            padding: 12px 30px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background: #218838;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 20px; padding: 0; }
            .resultat-box { background: #f8fafc; }
        }
    </style>
</head>
<body>
    <!-- Boutons d'action (non imprimables) -->
    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> Imprimer le résultat
        </button>
        <button onclick="window.close()" class="close-btn">
            <i class="fas fa-times"></i> Fermer
        </button>
        <button onclick="copyResult()" class="copy-btn">
            <i class="fas fa-copy"></i> Copier le résultat
        </button>
    </div>

    <!-- Contenu du document -->
    <div class="header">
        <h1>🏥 Hôpital Sendwe - Lubumbashi</h1>
        <div class="subtitle">Laboratoire d'analyses médicales</div>
        <div class="badge-print">Résultat d'examen</div>
    </div>

    <div class="info-section">
        <h3>📋 Informations générales</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">N° Examen:</span>
                <span class="info-value">{{ $examen->numero_examen ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Type:</span>
                <span class="info-value">{{ $examen->type_examen ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ isset($examen->date_examen) ? $examen->date_examen->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Heure:</span>
                <span class="info-value">{{ $examen->heure_examen ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Laborantin:</span>
                <span class="info-value">{{ $examen->laborantin->nom_complet ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Statut:</span>
                <span class="info-value">
                    @if($examen->statut == 'termine')
                        <span style="color: #28a745;">✅ Terminé</span>
                    @elseif($examen->statut == 'en_attente')
                        <span style="color: #f59e0b;">⏳ En attente</span>
                    @else
                        <span style="color: #dc3545;">❌ Annulé</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>👤 Informations patient</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nom complet:</span>
                <span class="info-value">{{ $examen->patient->nom_complet ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Numéro fiche:</span>
                <span class="info-value">{{ $examen->patient->numero_fiche ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date naissance:</span>
                <span class="info-value">{{ isset($examen->patient->date_naissance) ? $examen->patient->date_naissance->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">{{ $examen->patient->telephone ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Sexe:</span>
                <span class="info-value">{{ $examen->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Adresse:</span>
                <span class="info-value">{{ $examen->patient->adresse ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>🔬 Résultat d'analyse</h3>
        <div class="resultat-box">
            {!! nl2br(e($examen->resultat)) !!}
        </div>
    </div>

    <div class="footer">
        <div class="hospital-info">Hôpital Sendwe - Lubumbashi</div>
        <p>Document généré automatiquement par le système de gestion médicale</p>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
        <p style="margin-top: 5px; font-size: 10px; color: #bbb;">
            Ce document est une copie officielle du résultat d'examen.
        </p>
    </div>

    <script>
        function copyResult() {
            const resultatBox = document.querySelector('.resultat-box');
            const text = resultatBox.innerText.trim();
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('✅ Résultat copié dans le presse-papiers !');
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('✅ Résultat copié dans le presse-papiers !');
            } catch (err) {
                alert('❌ Erreur lors de la copie.');
            }
            document.body.removeChild(textarea);
        }

        // Impression automatique (décommenter si besoin)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>