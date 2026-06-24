<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport - Hôpital Sendwe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #1a4d8c;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #1a4d8c;
            margin: 0;
            font-size: 24px;
        }
        
        .hospital-logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .report-badge {
            display: inline-block;
            background: #1a4d8c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            margin-top: 10px;
        }
        
        .info-section {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .info-card {
            text-align: center;
            flex: 1;
        }
        
        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #1a4d8c;
            color: white;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
        }
        
        .stats-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            min-width: 100px;
        }
        
        .stat-number {
            font-size: 22px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 10px;
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: #1a4d8c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-print:hover {
            background: #0e3a6b;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print print-button">
        <button onclick="window.print()" class="btn-print">
            🖨️ Imprimer / Sauvegarder en PDF
        </button>
    </div>

    <div class="report-container">
        <!-- EN-TÊTE -->
        <div class="header">
            <div class="hospital-logo">🏥</div>
            <h1>Hôpital Sendwe - Lubumbashi</h1>
            <div class="report-badge">
                Rapport {{ $rapport['type'] == 'general' ? 'Général' : ($rapport['type'] == 'patients' ? 'des Patients' : ($rapport['type'] == 'consultations' ? 'des Consultations' : 'des Examens')) }}
            </div>
        </div>

        <!-- INFORMATIONS -->
        <div class="info-section">
            <div class="info-card">
                <div class="info-label">Période</div>
                <div class="info-value">{{ $rapport['periode']['libelle'] }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Date de génération</div>
                <div class="info-value">{{ $rapport['generated_at']->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Généré par</div>
                <div class="info-value">{{ $rapport['generated_by']->nom_complet ?? $rapport['generated_by']->name }}</div>
            </div>
        </div>

        <!-- SECTION PATIENTS -->
        @if(isset($rapport['patients']) && count($rapport['patients']) > 0)
        <div class="section">
            <div class="section-title">👥 Patients ({{ $rapport['total_patients'] ?? count($rapport['patients']) }})</div>
            
            @if(isset($rapport['patients_par_sexe']))
            <div class="stats-grid">
                <div class="stat-card" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <div class="stat-number">{{ $rapport['patients_par_sexe']['hommes'] ?? 0 }}</div>
                    <div class="stat-label">Hommes</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                    <div class="stat-number">{{ $rapport['patients_par_sexe']['femmes'] ?? 0 }}</div>
                    <div class="stat-label">Femmes</div>
                </div>
            </div>
            @endif
            
            <table>
                <thead>
                    <tr>
                        <th>N° fiche</th>
                        <th>Nom complet</th>
                        <th>Sexe</th>
                        <th>Téléphone</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport['patients'] as $p)
                    <tr>
                        <td>{{ $p->numero_fiche ?? 'N/A' }}</td>
                        <td>{{ $p->nom_complet ?? $p->name ?? 'N/A' }}</td>
                        <td>{{ $p->sexe == 'M' ? 'Homme' : ($p->sexe == 'F' ? 'Femme' : 'N/A') }}</td>
                        <td>{{ $p->telephone ?? 'N/A' }}</td>
                        <td>{{ Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- SECTION CONSULTATIONS -->
        @if(isset($rapport['consultations']) && count($rapport['consultations']) > 0)
        <div class="section">
            <div class="section-title">🩺 Consultations ({{ $rapport['total_consultations'] ?? count($rapport['consultations']) }})</div>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Médecin</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport['consultations'] as $c)
                    <tr>
                        <td>{{ $c->patient->nom_complet ?? $c->patient->name ?? 'N/A' }}</td>
                        <td>{{ $c->medecin->nom_complet ?? $c->medecin->name ?? 'N/A' }}</td>
                        <td>{{ Carbon\Carbon::parse($c->date_consultation)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- SECTION EXAMENS -->
        @if(isset($rapport['examens']) && count($rapport['examens']) > 0)
        <div class="section">
            <div class="section-title">🔬 Examens ({{ $rapport['total_examens'] ?? count($rapport['examens']) }})</div>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Type examen</th>
                        <th>Date examen</th>
                        <th>Résultat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport['examens'] as $e)
                    <tr>
                        <td>{{ $e->patient->nom_complet ?? $e->patient->name ?? 'N/A' }}</td>
                        <td>{{ $e->type_examen ?? 'N/A' }}</td>
                        <td>{{ Carbon\Carbon::parse($e->date_examen)->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($e->resultat ?? 'En attente', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- MESSAGE AUCUNE DONNÉE -->
        @if((!isset($rapport['patients']) || count($rapport['patients']) == 0) && 
            (!isset($rapport['consultations']) || count($rapport['consultations']) == 0) && 
            (!isset($rapport['examens']) || count($rapport['examens']) == 0))
        <div class="section">
            <div class="section-title">📊 Aucune donnée disponible</div>
            <p style="text-align: center; padding: 40px; color: #999;">
                Aucune donnée trouvée pour la période sélectionnée.
            </p>
        </div>
        @endif

        <!-- ACTIVITÉ PERSONNELLE -->
        @if(isset($rapport['mon_activite']))
        <div class="section">
            <div class="section-title">📈 Mon activité</div>
            <div class="stats-grid">
                <div class="stat-card" style="background: linear-gradient(135deg, #16a085 0%, #138d75 100%);">
                    <div class="stat-number">{{ $rapport['mon_activite'] }}</div>
                    <div class="stat-label">Patients enregistrés</div>
                </div>
            </div>
        </div>
        @endif

        <!-- PIED DE PAGE -->
        <div class="footer">
            <p>Document généré automatiquement par Hôpital Sendwe</p>
            <p>© {{ date('Y') }} Hôpital Sendwe - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>