@extends('layouts.laborantin')

@section('title', 'Laboratoire - Tableau de bord')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-microscope me-2" style="color: #1a4d6b;"></i>
                Laboratoire d'analyses
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} | 
                <i class="fas fa-clock ms-2 me-1"></i> {{ now()->format('H:i') }}
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.examens.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Nouvel examen
            </a>
        </div>
    </div>

    <!-- Cartes Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['examens_total'] ?? 0) }}</div>
                <div class="stat-label">Examens totaux</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">{{ number_format($stats['examens_attente'] ?? 0) }}</div>
                <div class="stat-label">En attente</div>
                @if(($stats['examens_attente'] ?? 0) > 0)
                <div class="stat-trend warning"><i class="fas fa-hourglass-half me-1"></i> À traiter</div>
                @endif
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['examens_termines'] ?? 0) }}</div>
                <div class="stat-label">Terminés</div>
                @if(($stats['examens_termines'] ?? 0) > 0)
                <div class="stat-trend up"><i class="fas fa-check me-1"></i> Résultats disponibles</div>
                @endif
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-percent"></i></div>
                <div class="stat-number">{{ number_format($stats['taux_realisation'] ?? 0) }}%</div>
                <div class="stat-label">Taux de réalisation</div>
            </div>
        </div>
    </div>

    <!-- Graphique principal et Types d'examens -->
    <div class="row g-4 mb-4">
        <div class="col-md-7" data-aos="fade-right">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: #1a4d6b;"></i>
                    Évolution des examens (6 derniers mois)
                </div>
                <div class="card-body">
                    <canvas id="examensChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5" data-aos="fade-left">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2" style="color: #1a4d6b;"></i>
                    Types d'examens les plus fréquents
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type examen</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topExamensTypes ?? [] as $examen)
                                <tr>
                                    <td>
                                        <i class="fas fa-vial me-2 text-primary"></i>
                                        {{ $examen->type_examen }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">{{ number_format($examen->total) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ round(($examen->total / max($stats['examens_total'], 1)) * 100, 1) }}%</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucune donnée disponible
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

    <!-- Statistiques détaillées -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-simple me-2" style="color: #1a4d6b;"></i>
                    Répartition par statut
                    <a href="{{ route('laborantin.statistiques.index') }}" class="btn btn-sm btn-outline-primary float-end">
                        <i class="fas fa-chart-line me-1"></i> Voir détails
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="statutChart" height="200"></canvas>
                    <div class="row mt-4 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="small text-muted">En attente</div>
                                <strong class="text-warning fs-4">{{ number_format($stats['examens_attente'] ?? 0) }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="small text-muted">En cours</div>
                                <strong class="text-info fs-4">{{ number_format($stats['examens_cours'] ?? 0) }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="small text-muted">Terminés</div>
                                <strong class="text-success fs-4">{{ number_format($stats['examens_termines'] ?? 0) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-calendar-week me-2" style="color: #1a4d6b;"></i>
                    Performance hebdomadaire
                </div>
                <div class="card-body">
                    @php
                        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                    @endphp
                    @foreach($performanceHebdomadaire ?? [] as $index => $perf)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold">{{ $jours[$index] ?? '' }}</span>
                            <span class="small text-muted">{{ $perf['realises'] ?? 0 }} examens</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ min(($perf['realises'] / max($perf['prevus'], 1)) * 100, 100) }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Évolution mensuelle -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: #1a4d6b;"></i>
                    Évolution mensuelle des examens
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mois</th>
                                    <th class="text-center">Nombre d'examens</th>
                                    <th class="text-center">Variation</th>
                                    <th>Tendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evolutionMensuelle ?? [] as $mois)
                                <tr>
                                    <td>
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        {{ $mois['mois'] }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ number_format($mois['total']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(($mois['variation'] ?? 0) > 0)
                                            <span class="text-success">
                                                <i class="fas fa-arrow-up"></i> +{{ $mois['variation'] }}%
                                            </span>
                                        @elseif(($mois['variation'] ?? 0) < 0)
                                            <span class="text-danger">
                                                <i class="fas fa-arrow-down"></i> {{ $mois['variation'] }}%
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i> 0%
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($mois['variation'] ?? 0) > 10)
                                            <span class="badge bg-success">📈 Forte hausse</span>
                                        @elseif(($mois['variation'] ?? 0) > 0)
                                            <span class="badge bg-info">📈 Hausse</span>
                                        @elseif(($mois['variation'] ?? 0) < -10)
                                            <span class="badge bg-danger">📉 Forte baisse</span>
                                        @elseif(($mois['variation'] ?? 0) < 0)
                                            <span class="badge bg-warning">📉 Baisse</span>
                                        @else
                                            <span class="badge bg-secondary">➡️ Stable</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                                        Aucune donnée disponible
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

    <!-- Examens en attente -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-hourglass-half me-2" style="color: #f59e0b;"></i>
                    Examens en attente de résultats
                    <span class="badge bg-warning ms-2">{{ $examensEnAttente->count() ?? 0 }} à traiter</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Examen</th>
                                    <th>Patient</th>
                                    <th>Type d'examen</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examensEnAttente ?? [] as $examen)
                                <tr>
                                    <td>
                                        <code>#{{ $examen->id_examen ?? $examen->id }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $examen->patient->nom_complet ?? $examen->patient->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $examen->patient->numero_fiche ?? '' }}</small>
                                    </td>
                                    <td>{{ $examen->type_examen ?? 'N/A' }}</div>
                                    <td>{{ isset($examen->date_examen) ? Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') : 'N/A' }}</div>
                                    <td>
                                        <a href="{{ route('laborantin.examens.resultat', $examen->id_examen ?? $examen->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Résultat
                                        </a>
                                        <a href="{{ route('laborantin.examens.show', $examen->id_examen ?? $examen->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                        Aucun examen en attente
                                    </div>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers examens réalisés -->
    <div class="row g-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-2" style="color: #1a4d6b;"></i>
                    Derniers examens réalisés
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Examen</th>
                                    <th>Résultat</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExamens ?? [] as $examen)
                                <tr>
                                    <td>{{ isset($examen->date_examen) ? Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') : 'N/A' }}</div>
                                    <td>{{ $examen->patient->nom_complet ?? $examen->patient->name ?? 'N/A' }}</div>
                                    <td>{{ $examen->type_examen ?? 'N/A' }}</div>
                                    <td>
                                        @if($examen->resultat)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-warning">En cours</span>
                                        @endif
                                    </div>
                                    <td>
                                        <a href="{{ route('laborantin.examens.show', $examen->id_examen ?? $examen->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($examen->resultat)
                                        <a href="#" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @endif
                                    </div>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucun examen réalisé
                                    </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique d'évolution des examens
        const ctx = document.getElementById('examensChart');
        if (ctx) {
            const chartLabels = <?php echo json_encode($chartLabels ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin']); ?>;
            const chartData = <?php echo json_encode($chartData ?? [0, 0, 0, 0, 0, 0]); ?>;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Examens réalisés',
                        data: chartData,
                        borderColor: '#1a4d6b',
                        backgroundColor: 'rgba(26, 77, 107, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#00bcd4',
                        pointBorderColor: '#1a4d6b',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Nombre d'examens"
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Graphique de répartition par statut
        const statutCtx = document.getElementById('statutChart');
        if (statutCtx) {
            const attente = {{ $stats['examens_attente'] ?? 0 }};
            const cours = {{ $stats['examens_cours'] ?? 0 }};
            const termines = {{ $stats['examens_termines'] ?? 0 }};
            const total = {{ $stats['examens_total'] ?? 0 }};
            
            new Chart(statutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'En cours', 'Terminés'],
                    datasets: [{
                        data: [attente, cours, termines],
                        backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    });
</script>
@endpush
@endsection