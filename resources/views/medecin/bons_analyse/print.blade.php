<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon d'analyse - {{ $bon->numero_bon }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            background: white;
            color: #1a1a1a;
            padding: 20px;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .bon-container {
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid #1a4d8c;
            padding: 25px;
            background: white;
        }
        
        /* En-tête */
        .header {
            text-align: center;
            border-bottom: 3px double #1a4d8c;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1a4d8c;
            letter-spacing: 2px;
        }
        
        .header .subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #1a4d8c;
            margin-top: 2px;
        }
        
        .header .ref {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .header .badge-priorite {
            display: inline-block;
            padding: 3px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
        }
        
        .badge-normal {
            background: #d4edda;
            color: #155724;
            border: 1px solid #28a745;
        }
        
        .badge-urgent {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #dc3545;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Grille d'informations */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-block {
            border: 1px solid #ddd;
            padding: 12px;
            background: #fafafa;
        }
        
        .info-block .block-title {
            font-weight: 700;
            font-size: 12px;
            color: #1a4d8c;
            border-bottom: 2px solid #1a4d8c;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-row {
            display: flex;
            padding: 3px 0;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: 600;
            width: 120px;
            flex-shrink: 0;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        /* Bilan Infirmier */
        .bilan-infirmier {
            border: 1px solid #17a2b8;
            padding: 12px;
            margin-bottom: 20px;
            background: #f0f9ff;
        }
        
        .bilan-infirmier .block-title {
            font-weight: 700;
            font-size: 12px;
            color: #0c5460;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .bilan-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 5px;
        }
        
        .bilan-item {
            background: white;
            padding: 5px 8px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        
        .bilan-item .label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .bilan-item .value {
            font-weight: 700;
            font-size: 12px;
            color: #1a4d8c;
        }
        
        /* Type échantillon */
        .echantillon-section {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
            background: #fafafa;
        }
        
        .echantillon-section .block-title {
            font-weight: 700;
            font-size: 12px;
            color: #1a4d8c;
            border-bottom: 2px solid #1a4d8c;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .echantillon-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 5px;
        }
        
        .echantillon-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .echantillon-option input[type="radio"] {
            accent-color: #1a4d8c;
            width: 14px;
            height: 14px;
        }
        
        .echantillon-option label {
            font-size: 11px;
        }
        
        .echantillon-option.checked {
            font-weight: 700;
            color: #1a4d8c;
        }
        
        /* Examens */
        .examens-section {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
            background: #fafafa;
        }
        
        .examens-section .block-title {
            font-weight: 700;
            font-size: 12px;
            color: #1a4d8c;
            border-bottom: 2px solid #1a4d8c;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .examens-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 5px 20px;
        }
        
        .examen-item {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 2px 0;
            font-size: 10px;
            border-bottom: 1px dotted #eee;
        }
        
        .examen-item.checked {
            font-weight: 600;
            color: #1a4d8c;
        }
        
        .examen-item .check {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #999;
            border-radius: 3px;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            flex-shrink: 0;
        }
        
        .examen-item.checked .check {
            border-color: #1a4d8c;
            background: #1a4d8c;
            color: white;
        }
        
        /* Observations */
        .observations-section {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
            background: #fafafa;
            min-height: 60px;
        }
        
        .observations-section .block-title {
            font-weight: 700;
            font-size: 12px;
            color: #1a4d8c;
            border-bottom: 2px solid #1a4d8c;
            padding-bottom: 5px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .observations-content {
            font-size: 11px;
            min-height: 30px;
        }
        
        /* Pied de page */
        .footer {
            text-align: center;
            border-top: 2px solid #1a4d8c;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 10px;
            color: #666;
        }
        
        .footer .contact {
            font-weight: 600;
            color: #1a4d8c;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            padding: 10px 30px;
            background: #1a4d8c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }
        
        .btn-print:hover {
            background: #0e3570;
        }
        
        .btn-close {
            padding: 10px 30px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }
        
        .btn-close:hover {
            background: #5a6268;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .bon-container {
                border: 1px solid #999;
                padding: 15px;
            }
            .badge-urgent {
                animation: none;
            }
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .examens-grid {
                grid-template-columns: 1fr 1fr;
            }
            .bilan-grid {
                grid-template-columns: 1fr 1fr;
            }
            .bon-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Boutons (non imprimables) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print me-2"></i> Imprimer le bon
        </button>
        <button onclick="window.close()" class="btn-close">
            ✖ Fermer
        </button>
        <button onclick="window.location.reload()" class="btn-print" style="background: #28a745;">
            <i class="fas fa-sync me-2"></i> Recharger
        </button>
    </div>

    <!-- Contenu du Bon -->
    <div class="bon-container" id="bon-content">
        
        <!-- EN-TÊTE -->
        <div class="header">
            <h1>HÔPITAL SENDWE</h1>
            <div class="subtitle">LABORATOIRE D'ANALYSES MÉDICALES</div>
            <div class="subtitle" style="font-size: 12px; color: #333;">Lubumbashi - République Démocratique du Congo</div>
            <div class="ref">Bon de demande d'analyse N° <strong>{{ $bon->numero_bon }}</strong></div>
            <div>
                <span class="badge-priorite {{ $bon->priorite == 'urgent' ? 'badge-urgent' : 'badge-normal' }}">
                    {{ $bon->priorite == 'urgent' ? '⚠️ URGENT' : '📋 Normal' }}
                </span>
            </div>
        </div>

        <!-- GRILLE INFORMATIONS -->
        <div class="info-grid">
            <!-- Patient -->
            <div class="info-block">
                <div class="block-title">👤 Informations patient</div>
                <div class="info-row">
                    <span class="info-label">Nom, Post-nom, Prénom :</span>
                    <span class="info-value"><strong>{{ $bon->patient->nom_complet ?? 'N/A' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sexe :</span>
                    <span class="info-value">{{ $bon->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date naissance :</span>
                    <span class="info-value">{{ isset($bon->patient->date_naissance) ? \Carbon\Carbon::parse($bon->patient->date_naissance)->format('d/m/Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $bon->patient->telephone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Hospitalisé(e) :</span>
                    <span class="info-value">
                        {{ $bon->hospitalise ? 'Oui' : 'Non' }}
                        @if($bon->hospitalise)
                            - Pavillon {{ $bon->pavillon ?? 'N/A' }}, Chambre {{ $bon->chambre ?? 'N/A' }}, Lit {{ $bon->lit ?? 'N/A' }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresse :</span>
                    <span class="info-value">{{ $bon->adresse_domicile ?? $bon->patient->adresse ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Médecin demandeur -->
            <div class="info-block">
                <div class="block-title">👨‍⚕️ Médecin/Clinicién demandeur</div>
                <div class="info-row">
                    <span class="info-label">Nom et prénom :</span>
                    <span class="info-value"><strong>Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date demande :</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($bon->date_demande)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Heure :</span>
                    <span class="info-value">{{ $bon->heure_demande }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service :</span>
                    <span class="info-value">{{ $bon->service_demandeur }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $bon->telephone_demandeur }}</span>
                </div>
                <div class="info-row" style="margin-top: 5px; border-top: 1px dashed #ddd; padding-top: 5px;">
                    <span class="info-label">Signature :</span>
                    <span class="info-value" style="font-family: cursive; font-size: 16px; color: #1a4d8c;">Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</span>
                </div>
            </div>
        </div>

        <!-- ⭐ BILAN INFIRMIER - AJOUTÉ ⭐ -->
        @if(isset($bon->poids_infirmier) || isset($bon->taille_infirmier) || isset($bon->temperature_infirmier) || isset($bon->tension_arterielle_infirmier))
        <div class="bilan-infirmier">
            <div class="block-title">
                <i class="fas fa-user-nurse me-2"></i> Bilan initial - Infirmier
                @if($bon->infirmier)
                <span style="font-weight: normal; font-size: 11px; color: #0c5460;">
                    ({{ $bon->infirmier->nom_complet ?? 'N/A' }})
                </span>
                @endif
            </div>
            <div class="bilan-grid">
                @if($bon->poids_infirmier)
                <div class="bilan-item">
                    <div class="label">Poids</div>
                    <div class="value">{{ $bon->poids_infirmier }} kg</div>
                </div>
                @endif
                @if($bon->taille_infirmier)
                <div class="bilan-item">
                    <div class="label">Taille</div>
                    <div class="value">{{ $bon->taille_infirmier }} cm</div>
                </div>
                @endif
                @if($bon->temperature_infirmier)
                <div class="bilan-item">
                    <div class="label">Température</div>
                    <div class="value">{{ $bon->temperature_infirmier }} °C</div>
                </div>
                @endif
                @if($bon->tension_arterielle_infirmier)
                <div class="bilan-item">
                    <div class="label">Tension</div>
                    <div class="value">{{ $bon->tension_arterielle_infirmier }}</div>
                </div>
                @endif
                @if($bon->frequence_cardiaque_infirmier)
                <div class="bilan-item">
                    <div class="label">FC</div>
                    <div class="value">{{ $bon->frequence_cardiaque_infirmier }} bpm</div>
                </div>
                @endif
                @if($bon->frequence_respiratoire_infirmier)
                <div class="bilan-item">
                    <div class="label">FR</div>
                    <div class="value">{{ $bon->frequence_respiratoire_infirmier }}/min</div>
                </div>
                @endif
                @if($bon->imc_infirmier)
                <div class="bilan-item">
                    <div class="label">IMC</div>
                    <div class="value">{{ $bon->imc_infirmier }}</div>
                </div>
                @endif
                @if($bon->spo2_infirmier)
                <div class="bilan-item">
                    <div class="label">SpO2</div>
                    <div class="value">{{ $bon->spo2_infirmier }}%</div>
                </div>
                @endif
            </div>
            @if($bon->motif_infirmier || $bon->symptomes_infirmier || $bon->observations_infirmier)
            <div style="margin-top: 10px; border-top: 1px dashed #17a2b8; padding-top: 10px;">
                @if($bon->motif_infirmier)
                <div class="info-row">
                    <span class="info-label" style="width: 80px;">Motif :</span>
                    <span class="info-value">{{ $bon->motif_infirmier }}</span>
                </div>
                @endif
                @if($bon->symptomes_infirmier)
                <div class="info-row">
                    <span class="info-label" style="width: 80px;">Symptômes :</span>
                    <span class="info-value">{{ $bon->symptomes_infirmier }}</span>
                </div>
                @endif
                @if($bon->observations_infirmier)
                <div class="info-row">
                    <span class="info-label" style="width: 80px;">Observations :</span>
                    <span class="info-value">{{ $bon->observations_infirmier }}</span>
                </div>
                @endif
            </div>
            @endif
            @if($bon->allergies_infirmier || $bon->traitements_infirmier)
            <div style="margin-top: 5px;">
                @if($bon->allergies_infirmier)
                <div class="info-row" style="color: #dc3545;">
                    <span class="info-label" style="width: 80px;">⚠️ Allergies :</span>
                    <span class="info-value"><strong>{{ $bon->allergies_infirmier }}</strong></span>
                </div>
                @endif
                @if($bon->traitements_infirmier)
                <div class="info-row">
                    <span class="info-label" style="width: 80px;">Traitements :</span>
                    <span class="info-value">{{ $bon->traitements_infirmier }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- PRÉLEVEUR -->
        <div class="info-block" style="margin-bottom: 20px;">
            <div class="block-title">💉 Préleveur (labo/autre)</div>
            <div class="info-row">
                <span class="info-label">Nom et prénom :</span>
                <span class="info-value">{{ $bon->nom_preleveur ?? '___________________' }} {{ $bon->prenom_preleveur ?? '___________________' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date prélèvement :</span>
                <span class="info-value">{{ $bon->date_prelevement ? \Carbon\Carbon::parse($bon->date_prelevement)->format('d/m/Y') : '____/____/________' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Heure :</span>
                <span class="info-value">{{ $bon->heure_prelevement ?? '____:____' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">N° Id. Client/labo :</span>
                <span class="info-value">{{ $bon->id_client_labo ?? '___________________' }}</span>
            </div>
        </div>

        <!-- TYPE ÉCHANTILLON -->
        <div class="echantillon-section">
            <div class="block-title">🧪 Types d'échantillons primaires</div>
            <div class="echantillon-options">
                @php
                    $types = ['sang' => 'Sang', 'selles' => 'Selles', 'urines' => 'Urines', 'lcr' => 'LCR', 'crachat' => 'Crachat', 'autres' => 'Autres'];
                @endphp
                @foreach($types as $key => $label)
                <div class="echantillon-option {{ $bon->type_echantillon == $key ? 'checked' : '' }}">
                    <input type="radio" {{ $bon->type_echantillon == $key ? 'checked' : '' }} disabled>
                    <label>{{ $label }}</label>
                </div>
                @endforeach
            </div>
            @if($bon->autres_echantillon)
            <div class="info-row" style="margin-top: 5px;">
                <span class="info-label">Précision :</span>
                <span class="info-value"><strong>{{ $bon->autres_echantillon }}</strong></span>
            </div>
            @endif
        </div>

        <!-- EXAMENS -->
        <div class="examens-section">
            <div class="block-title">🔬 Examens demandés</div>
            <div class="examens-grid">
                @php
                    $categories = [
                        'parasitologie' => ['label' => '🦠 Parasitologie', 'items' => $bon->examens['parasitologie'] ?? []],
                        'hematologie' => ['label' => '🩸 Hématologie', 'items' => $bon->examens['hematologie'] ?? []],
                        'microbiologie' => ['label' => '🧫 Microbiologie', 'items' => $bon->examens['microbiologie'] ?? []],
                        'biochimie' => ['label' => '🧪 Biochimie', 'items' => $bon->examens['biochimie'] ?? []],
                        'immuno_serologie' => ['label' => '🧬 Immuno-sérologie', 'items' => $bon->examens['immuno_serologie'] ?? []]
                    ];
                    
                    $examensList = [];
                    foreach($categories as $catKey => $cat) {
                        foreach($cat['items'] as $key => $value) {
                            if($value == 1) {
                                $examensList[] = $cat['label'] . ' : ' . $key;
                            }
                        }
                    }
                @endphp
                
                @if(count($examensList) > 0)
                    @foreach($examensList as $examen)
                    <div class="examen-item checked">
                        <span class="check">✓</span>
                        <span>{{ $examen }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="examen-item" style="grid-column: 1 / -1; color: #999; text-align: center; padding: 10px;">
                        Aucun examen sélectionné
                    </div>
                @endif
            </div>
        </div>

        <!-- OBSERVATIONS CLINIQUES -->
        <div class="observations-section">
            <div class="block-title">📋 Informations cliniques</div>
            <div class="observations-content">
                {{ $bon->observations_cliniques ?? 'Aucune observation clinique' }}
            </div>
        </div>

        <!-- SIGNATURE -->
        <div style="display: flex; justify-content: space-between; margin: 15px 0; padding-top: 10px; border-top: 1px solid #ddd;">
            <div>
                <div style="font-size: 10px; color: #999;">Date de la demande</div>
                <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($bon->date_demande)->format('d/m/Y') }}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 10px; color: #999;">Signature du clinicien demandeur</div>
                <div style="font-family: cursive; font-size: 18px; color: #1a4d8c;">Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</div>
            </div>
        </div>

        <!-- PIED DE PAGE -->
        <div class="footer">
            <div class="contact">Hôpital Sendwe - Lubumbashi</div>
            <div>📞 +243 898 479 577 / +243 976 021 909</div>
            <div>laboratoiresendwe@gmail.com</div>
            <div style="margin-top: 8px; font-size: 9px;">
                Document généré automatiquement le {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <script>
        // Impression automatique après chargement (décommenter pour auto-print)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>