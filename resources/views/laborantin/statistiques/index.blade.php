@extends('layouts.laborantin')

@section('title', 'Statistiques - Laborantin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">
                <i class="fas fa-chart-bar me-2" style="color: #1a4d6b;"></i>
                Tableau de bord des statistiques
            </h1>
            <p class="text-muted mb-0">Analyse des performances et des examens du laboratoire</p>
        </div>
        <div>
            <a href="{{ route('laborantin.statistiques.export-pdf') }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Exporter PDF
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Examens</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_examens'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-microscope fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Terminés</h6>
                            <h3 class="mb-0">{{ number_format($stats['examens_termines'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">En attente</h6>
                            <h3 class="mb-0">{{ number_format($stats['examens_en_attente'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Taux réalisation</h6>
                            <h3 class="mb-0">{{ number_format($tauxRealisation ?? 0) }}%</h3>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques ligne 1 -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2" style="color: #1a4d6b;"></i>
                    Examens par type
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2" style="color: #1a4d6b;"></i>
                    Répartition par statut
                </div>
                <div class="card-body">
                    <canvas id="statutChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique évolution -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: #1a4d6b;"></i>
                    Évolution mensuelle des examens (12 derniers mois)
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top examens et derniers examens -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-trophy me-2" style="color: #1a4d6b;"></i>
                    Top 5 types d'examens
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($topExamens ?? [] as $index => $examen)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary rounded-pill me-2">{{ $index + 1 }}</span>
                                    <i class="fas fa-flask text-primary me-2"></i>
                                    <strong>{{ $examen->type_examen ?? 'N/A' }}</strong>
                                </div>
                                <span class="badge bg-secondary rounded-pill">{{ number_format($examen->total ?? 0) }} examens</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-history me-2" style="color: #1a4d6b;"></i>
                    Derniers examens traités
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($derniersExamens ?? [] as $examen)
                                <tr>
                                    <td>{{ $examen->patient->nom_complet ?? $examen->patient->name ?? 'N/A' }}</div>
                                    <td>{{ $examen->type_examen ?? 'N/A' }}</div>
                                    <td>
                                        <span class="badge {{ $examen->statut == 'termine' ? 'bg-success' : ($examen->statut == 'en_cours' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ $examen->statut == 'termine' ? 'Terminé' : ($examen->statut == 'en_cours' ? 'En cours' : 'En attente') }}
                                        </span>
                                    </div>
                                    <td>{{ isset($examen->updated_at) ? $examen->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                </table>
                                @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">Aucun examen traité</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance hebdomadaire -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-week me-2" style="color: #1a4d6b;"></i>
                    Performance hebdomadaire (7 derniers jours)
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($performanceHebdo ?? [] as $perf)
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="mb-2">{{ $perf['jour'] ?? '' }}</h6>
                                    <div class="display-6 mb-2">{{ $perf['total'] ?? 0 }}</div>
                                    <div class="small text-muted">examens réalisés</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ min(($perf['termines'] ?? 0) / max($perf['total'], 1) * 100, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $perf['termines'] ?? 0 }} terminés</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique par type
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: @json($typeLabels ?? []),
                datasets: [{
                    label: "Nombre d'examens",
                    data: @json($typeData ?? []),
                    backgroundColor: 'rgba(26, 77, 107, 0.7)',
                    borderColor: '#1a4d6b',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { position: 'top' } }
            }
        });
    }

    // Graphique par statut
    const statutCtx = document.getElementById('statutChart');
    if (statutCtx) {
        new Chart(statutCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'En cours', 'Terminés'],
                datasets: [{
                    data: [
                        {{ $stats['examens_en_attente'] ?? 0 }},
                        {{ $stats['examens_en_cours'] ?? 0 }},
                        {{ $stats['examens_termines'] ?? 0 }}
                    ],
                    backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = {{ $stats['total_examens'] ?? 0 }};
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

    // Graphique évolution
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: @json($evolutionLabels ?? []),
                datasets: [{
                    label: "Nombre d'examens",
                    data: @json($evolutionData ?? []),
                    borderColor: '#1a4d6b',
                    backgroundColor: 'rgba(26, 77, 107, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#00bcd4',
                    pointBorderColor: '#1a4d6b',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
</script>
@endpush
@endsection