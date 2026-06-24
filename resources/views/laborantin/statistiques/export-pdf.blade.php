<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Statistiques - Hôpital Sendwe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #1a4d6b;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #1a4d6b;
            margin: 0;
            font-size: 24px;
        }
        
        .hospital-logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .report-badge {
            display: inline-block;
            background: #1a4d6b;
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
            border-radius: 8px;
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
            background: #1a4d6b;
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
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #1a4d6b 0%, #0f3b53 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 5px;
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
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #10b981;
            color: white;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="hospital-logo">🏥</div>
        <h1>Hôpital Sendwe - Lubumbashi</h1>
        <p>Rapport des statistiques du laboratoire</p>
        <div class="report-badge">Période : {{ $stats['periode']['libelle'] ?? 'Toutes périodes' }}</div>
    </div>

    <div class="info-section">
        <div class="info-card">
            <div class="info-label">Date de génération</div>
            <div class="info-value">{{ $stats['generated_at']->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Généré par</div>
            <div class="info-value">{{ $stats['generated_by']->nom_complet ?? $stats['generated_by']->name }}</div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="section">
        <div class="section-title">📊 Vue d'ensemble</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total_examens'] ?? 0) }}</div>
                <div class="stat-label">Total examens</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-number">{{ number_format($stats['examens_termines'] ?? 0) }}</div>
                <div class="stat-label">Examens terminés</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-number">{{ number_format(($stats['examens_termines'] ?? 0) / max($stats['total_examens'], 1) * 100, 1) }}%</div>
                <div class="stat-label">Taux de réalisation</div>
            </div>
        </div>
    </div>

    <!-- Examens par type -->
    <div class="section">
        <div class="section-title">🔬 Examens par type</div>
        <table>
            <thead>
                <tr>
                    <th>Type d'examen</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['examens_par_type'] ?? [] as $examen)
                <tr>
                    <td>{{ $examen->type_examen }}</div>
                    <td class="text-center">{{ number_format($examen->total) }}</div>
                    <td class="text-center">{{ round(($examen->total / max($stats['total_examens'], 1)) * 100, 1) }}%</div>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">Aucune donnée disponible</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Document généré automatiquement par Hôpital Sendwe - Système de gestion hospitalière</p>
        <p>© {{ date('Y') }} Hôpital Sendwe - Lubumbashi, RDC - Tous droits réservés</p>
    </div>
</body>
</html>