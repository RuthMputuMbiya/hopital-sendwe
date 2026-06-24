@extends('layouts.gestionnaire')

@section('title', 'Tableau des statistiques')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>
                Tableau des statistiques
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-pie me-1"></i> Vue d'ensemble de l'activité de l'hôpital
            </p>
        </div>
        
        </div>
    </div>

    <!-- Cartes KPI -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
                <div class="stat-label">Utilisateurs</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total_patients']) }}</div>
                <div class="stat-label">Patients</div>
                <div class="stat-trend {{ $tendances['patients']['direction'] == 'up' ? 'up' : 'down' }}">
                    <i class="fas fa-arrow-{{ $tendances['patients']['direction'] }} me-1"></i>
                    {{ $tendances['patients']['pourcentage'] }}% ce mois
                </div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number">{{ number_format($stats['total_consultations']) }}</div>
                <div class="stat-label">Consultations</div>
                <div class="stat-trend {{ $tendances['consultations']['direction'] == 'up' ? 'up' : 'down' }}">
                    <i class="fas fa-arrow-{{ $tendances['consultations']['direction'] }} me-1"></i>
                    {{ $tendances['consultations']['pourcentage'] }}% ce mois
                </div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['total_examens']) }}</div>
                <div class="stat-label">Examens</div>
                <div class="stat-trend {{ $tendances['examens']['direction'] == 'up' ? 'up' : 'down' }}">
                    <i class="fas fa-arrow-{{ $tendances['examens']['direction'] }} me-1"></i>
                    {{ $tendances['examens']['pourcentage'] }}% ce mois
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique évolution -->
    <div class="row g-4 mb-4">
        <div class="col-md-8" data-aos="fade-right">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>
                    Évolution des consultations (12 mois)
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="320"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2" style="color: var(--secondary);"></i>
                    Utilisateurs par rôle
                </div>
                <div class="card-body">
                    <canvas id="usersByRoleChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Maladies fréquentes et Top médecins -->
    <div class="row g-4 mb-4">
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-disease me-2" style="color: var(--secondary);"></i>
                    Maladies les plus fréquentes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Maladie</th>
                                    <th>Cas</th>
                                    <th>Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topMaladies as $maladie)
                                <tr>
                                    <td><strong>{{ $maladie->nom_maladie }}</strong></td>
                                    <td>{{ number_format($maladie->consultations_count) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ ($maladie->consultations_count / max($stats['total_consultations'], 1)) * 100 }}%">
                                                </div>
                                            </div>
                                            <span class="ms-2 small">
                                                {{ number_format(($maladie->consultations_count / max($stats['total_consultations'], 1)) * 100, 1) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4">Aucune donnée</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2" style="color: var(--secondary);"></i>
                    Top 5 Médecins - Consultations
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Médecin</th>
                                    <th>Consultations</th>
                                    <th>Spécialité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topMedecins as $medecin)
                                <tr>
                                    <td>
                                        <strong>{{ $medecin->prenom }} {{ $medecin->nom }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($medecin->consultations_effectuees_count) }}</span>
                                    </td>
                                    <td>{{ $medecin->specialite ?? 'Généraliste' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4">Aucune donnée</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($moisLabels) !!},
            datasets: [{
                label: 'Nombre de consultations',
                data: {!! json_encode($consultationsEvolution) !!},
                borderColor: '#1a4d8c',
                backgroundColor: 'rgba(26, 77, 140, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#1a4d8c',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Consultations' } } }
        }
    });
    
    // Graphique des utilisateurs par rôle
    new Chart(document.getElementById('usersByRoleChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($usersByRole->pluck('nom_role')) !!},
            datasets: [{
                data: {!! json_encode($usersByRole->pluck('users_count')) !!},
                backgroundColor: ['#1a4d8c', '#2c6e9e', '#3498db', '#5dade2', '#85c1e9']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
@endsection