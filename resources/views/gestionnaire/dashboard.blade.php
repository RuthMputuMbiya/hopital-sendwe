@extends('layouts.gestionnaire')

@section('title', 'Tableau de bord - Gestionnaire')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem 1.25rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border-left: 4px solid #1a4d8c;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.10);
    }
    .stat-card .stat-icon {
        font-size: 1.8rem;
        color: #1a4d8c;
        margin-bottom: 0.5rem;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }
    .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .stat-card .stat-trend {
        font-size: 0.75rem;
        margin-top: 0.5rem;
        padding: 0.2rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
        font-weight: 500;
    }
    .stat-trend.up {
        background: #d4edda;
        color: #155724;
    }
    .stat-trend.down {
        background: #f8d7da;
        color: #721c24;
    }
    .stat-trend.stable {
        background: #e2e3e5;
        color: #383d41;
    }
    .stat-trend.warning {
        background: #fff3e0;
        color: #92400e;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 5px 30px rgba(0,0,0,0.10);
    }
    .card-header {
        background: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
        font-weight: 600;
        border-radius: 15px 15px 0 0 !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .card-body {
        padding: 1.5rem;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8f9fa;
        padding: 0.75rem 1rem;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
    }
    .badge-success {
        background: #28a745;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .badge-primary {
        background: #1a4d8c;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .badge-warning {
        background: #ffc107;
        color: #212529;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .progress {
        background: #e9ecef;
        border-radius: 10px;
        height: 6px;
    }
    .progress-bar {
        background: linear-gradient(90deg, #1a4d8c, #d4af37);
        border-radius: 10px;
    }
    .btn-sm {
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 8px;
    }
    .btn-outline-primary {
        color: #1a4d8c;
        border-color: #1a4d8c;
        background: transparent;
        border: 1.5px solid #1a4d8c;
    }
    .btn-outline-primary:hover {
        background: #1a4d8c;
        color: white;
    }
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background: transparent;
        border: 1.5px solid #6c757d;
    }
    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.5rem;
        }
        .stat-card {
            padding: 1rem;
        }
        .container-fluid {
            padding: 0 0.75rem;
        }
        .table-responsive {
            font-size: 0.8rem;
        }
        .table td, .table th {
            padding: 0.5rem 0.6rem;
        }
    }
    @media print {
        .stat-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .no-print {
            display: none !important;
        }
        .stat-trend {
            display: none !important;
        }
        .btn-sm {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 mb-1">
                <i class="fas fa-tachometer-alt me-2" style="color: #d4af37;"></i>
                Tableau de bord - Gestionnaire
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} | 
                <i class="fas fa-clock ms-2 me-1"></i> 
                {{ now()->format('H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap no-print">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer
            </button>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>

    <!-- ============================================================
    STATISTIQUES LIGNE 1 - PERSONNEL
    ============================================================ -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                <div class="stat-number">{{ number_format($stats['total_medecins'] ?? 0) }}</div>
                <div class="stat-label">Médecins</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Actifs</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-nurse"></i></div>
                <div class="stat-number">{{ number_format($stats['total_receptionnistes'] ?? 0) }}</div>
                <div class="stat-label">Réceptionnistes</div>
                <div class="stat-trend stable"><i class="fas fa-minus me-1"></i> En service</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-microscope"></i></div>
                <div class="stat-number">{{ number_format($stats['total_laborantins'] ?? 0) }}</div>
                <div class="stat-label">Laborantins</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Qualifiés</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-nurse" style="color: #f59e0b;"></i></div>
                <div class="stat-number">{{ number_format($stats['total_infirmiers'] ?? 0) }}</div>
                <div class="stat-label">Infirmiers</div>
                <div class="stat-trend warning"><i class="fas fa-arrow-up me-1"></i> 🩺 En service</div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    STATISTIQUES LIGNE 2 - ACTIVITÉ
    ============================================================ -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total_patients'] ?? 0) }}</div>
                <div class="stat-label">Patients</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Total général</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number">{{ number_format($stats['total_consultations'] ?? 0) }}</div>
                <div class="stat-label">Consultations</div>
                <div class="stat-trend up">
                    <i class="fas fa-calendar me-1"></i> 
                    Mois: {{ number_format($stats['consultations_mois'] ?? 0) }}
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['total_examens'] ?? 0) }}</div>
                <div class="stat-label">Examens</div>
                <div class="stat-trend up"><i class="fas fa-chart-line me-1"></i> En progression</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-disease"></i></div>
                <div class="stat-number">{{ number_format($stats['total_maladies'] ?? 0) }}</div>
                <div class="stat-label">Maladies</div>
                <div class="stat-trend stable"><i class="fas fa-database me-1"></i> Répertoriées</div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    STATISTIQUES LIGNE 3 - BONS D'ANALYSE ET ACTIVITÉ
    ============================================================ -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-medical" style="color: #17a2b8;"></i></div>
                <div class="stat-number">{{ number_format($stats['total_bons_analyse'] ?? 0) }}</div>
                <div class="stat-label">Bons d'analyse</div>
                <div class="stat-trend stable"><i class="fas fa-database me-1"></i> Enregistrés</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ number_format($stats['patients_aujourdhui'] ?? 0) }}</div>
                <div class="stat-label">Patients aujourd'hui</div>
                <div class="stat-trend up"><i class="fas fa-user-plus me-1"></i> Nouveaux</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number">{{ number_format($stats['consultations_aujourdhui'] ?? 0) }}</div>
                <div class="stat-label">Consultations aujourd'hui</div>
                <div class="stat-trend up"><i class="fas fa-stethoscope me-1"></i> Aujourd'hui</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number">{{ number_format($stats['taux_activite'] ?? 0) }}%</div>
                <div class="stat-label">Taux d'activité</div>
                <div class="stat-trend up"><i class="fas fa-trend-up me-1"></i> En hausse</div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    GRAPHIQUES
    ============================================================ -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: #d4af37;"></i>
                    Évolution des consultations (6 derniers mois)
                </div>
                <div class="card-body" style="min-height: 280px;">
                    <canvas id="consultationsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-disease me-2" style="color: #d4af37;"></i>
                    Maladies les plus fréquentes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Maladie</th>
                                    <th class="text-center">Cas</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($maladiesStats ?? []) as $maladie)
                                <tr>
                                    <td><strong>{{ $maladie->nom_maladie ?? 'Non spécifiée' }}</strong></td>
                                    <td class="text-center">{{ number_format($maladie->total ?? 0) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1">
                                                <div class="progress-bar" 
                                                     style="width: {{ ($maladie->total / max($stats['total_consultations'], 1)) * 100 }}%;">
                                                </div>
                                            </div>
                                            <span class="ms-2 small fw-bold">
                                                {{ number_format(($maladie->total / max($stats['total_consultations'], 1)) * 100, 1) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>Aucune donnée
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    DERNIÈRES CONSULTATIONS
    ============================================================ -->
    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <i class="fas fa-clock me-2" style="color: #d4af37;"></i>
                        Dernières consultations
                    </div>
                    <span class="badge badge-primary">{{ isset($recentConsultations) ? $recentConsultations->count() : 0 }} récentes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Médecin</th>
                                    <th>Infirmier</th>
                                    <th>Diagnostic</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recentConsultations ?? []) as $consultation)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>
                                        @if($consultation->infirmier)
                                            <span class="badge badge-warning">🩺 {{ $consultation->infirmier->nom_complet }}</span>
                                        @else
                                            <span class="badge badge-secondary">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($consultation->diagnostic ?? 'Non renseigné', 40) }}</td>
                                    <td>
                                        @if($consultation->statut_consultation == 'termine' || $consultation->statut == 'termine')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle me-1"></i> Terminé
                                            </span>
                                        @elseif($consultation->statut_consultation == 'en_attente_infirmier')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock me-1"></i> En attente infirmier
                                            </span>
                                        @elseif($consultation->statut_consultation == 'bilan_fait')
                                            <span class="badge badge-info">
                                                <i class="fas fa-check me-1"></i> Bilan fait
                                            </span>
                                        @elseif($consultation->statut_consultation == 'en_consultation')
                                            <span class="badge badge-primary">
                                                <i class="fas fa-stethoscope me-1"></i> En consultation
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ $consultation->statut ?? 'En cours' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                        Aucune consultation récente
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('consultationsChart').getContext('2d');
        
        var chartLabels = <?php echo json_encode($chartLabels ?? ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin']); ?>;
        var chartData = <?php echo json_encode($chartData ?? [0, 0, 0, 0, 0, 0]); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Nombre de consultations',
                    data: chartData,
                    borderColor: '#1a4d8c',
                    backgroundColor: 'rgba(26, 77, 140, 0.08)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#d4af37',
                    pointBorderColor: '#1a4d8c',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { weight: '600', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Consultations: ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre de consultations',
                            font: { weight: 'bold', size: 12 }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mois',
                            font: { weight: 'bold', size: 12 }
                        },
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    });
</script>
@endpush