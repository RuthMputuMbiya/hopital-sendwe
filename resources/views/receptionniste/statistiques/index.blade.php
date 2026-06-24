@extends('layouts.receptionniste')

@section('title', 'Statistiques')

@section('page-title', 'Tableau des statistiques')
@section('page-subtitle', 'Vue d\'ensemble de l\'activité')

@section('content')
<div class="container-fluid">
    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('receptionniste.statistiques.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Période</label>
                    <select name="periode" class="form-select">
                        <option value="semaine" {{ $periode == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="mois" {{ $periode == 'mois' ? 'selected' : '' }}>Ce mois</option>
                        <option value="annee" {{ $periode == 'annee' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        <i class="fas fa-filter me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes Patients -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($statsPatients['total']) }}</div>
                <div class="stat-label">Total patients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                <div class="stat-number">{{ number_format($statsPatients['mois_actuel']) }}</div>
                <div class="stat-label">Nouveaux ce mois</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up me-1"></i>
                    @php
                        $variation = $statsPatients['mois_actuel'] - $statsPatients['mois_precedent'];
                        $pourcentage = $statsPatients['mois_precedent'] > 0 ? round(($variation / $statsPatients['mois_precedent']) * 100, 1) : 0;
                    @endphp
                    +{{ $pourcentage }}% vs mois dernier
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-mars"></i></div>
                <div class="stat-number">{{ number_format($statsPatients['hommes']) }}</div>
                <div class="stat-label">Hommes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-venus"></i></div>
                <div class="stat-number">{{ number_format($statsPatients['femmes']) }}</div>
                <div class="stat-label">Femmes</div>
            </div>
        </div>
    </div>

    <!-- Cartes Consultations et Examens -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number">{{ number_format($statsConsultations['total']) }}</div>
                <div class="stat-label">Consultations totales</div>
                <div class="stat-trend up">{{ $statsConsultations['mois'] }} ce mois</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($statsExamens['total']) }}</div>
                <div class="stat-label">Examens totaux</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($statsExamens['termines']) }}</div>
                <div class="stat-label">Examens terminés</div>
            </div>
        </div>
    </div>

    <!-- Graphique évolution patients -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Évolution des patients (12 mois)
                </div>
                <div class="card-body">
                    <canvas id="patientsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Évolution des consultations (12 mois)
                </div>
                <div class="card-body">
                    <canvas id="consultationsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Médecins et Top Examens -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2"></i> Top Médecins
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Médecin</th><th>Consultations</th></tr>
                            </thead>
                            <tbody>
                                @foreach($topMedecins as $medecin)
                                <tr>
                                    <td>{{ $medecin->prenom }} {{ $medecin->nom }}</small></td>
                                    <td><span class="badge bg-primary">{{ $medecin->consultations_effectuees_count }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-flask me-2"></i> Top Examens
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Type</th><th>Nombre</th></tr></thead>
                            <tbody>
                                @foreach($topExamens as $examen)
                                <tr>
                                    <td>{{ $examen->type_examen }}</small></td>
                                    <td><span class="badge bg-info">{{ $examen->total }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        <table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par âge -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Répartition par âge
                </div>
                <div class="card-body">
                    <canvas id="ageChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique patients
    new Chart(document.getElementById('patientsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($moisLabels) !!},
            datasets: [{
                label: 'Nouveaux patients',
                data: {!! json_encode($evolutionPatients) !!},
                borderColor: '#2c6e9e',
                backgroundColor: 'rgba(44,110,158,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    // Graphique consultations
    new Chart(document.getElementById('consultationsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($moisLabels) !!},
            datasets: [{
                label: 'Consultations',
                data: {!! json_encode($evolutionConsultations) !!},
                borderColor: '#d4af37',
                backgroundColor: 'rgba(212,175,55,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    // Graphique âge
    new Chart(document.getElementById('ageChart'), {
        type: 'pie',
        data: {
            labels: ['Enfants (<18 ans)', 'Adultes (18-65 ans)', 'Seniors (>65 ans)'],
            datasets: [{
                data: [{{ $statsPatients['enfants'] }}, {{ $statsPatients['adultes'] }}, {{ $statsPatients['seniors'] }}],
                backgroundColor: ['#2c6e9e', '#d4af37', '#e74c3c']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
@endsection