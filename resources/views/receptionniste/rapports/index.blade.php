@extends('layouts.receptionniste')

@section('title', 'Rapports et statistiques')

@section('page-title', 'Rapports et statistiques')
@section('page-subtitle', 'Analyse de votre activité')

@section('content')
<div class="container-fluid">
    <!-- Cartes statistiques globales -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total_patients']) }}</div>
                <div class="stat-label">Total patients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-stethoscope"></i></div>
                <div class="stat-number">{{ number_format($stats['total_consultations']) }}</div>
                <div class="stat-label">Consultations totales</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['total_examens']) }}</div>
                <div class="stat-label">Examens totaux</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number">{{ number_format($stats['patients_mois']) }}</div>
                <div class="stat-label">Nouveaux ce mois</div>
            </div>
        </div>
    </div>

    <!-- Mon activité -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <i class="fas fa-user-circle me-2 text-primary"></i> Mon activité
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Patients enregistrés</span>
                        <span class="badge bg-primary">{{ number_format($monActivite['patients_enregistres']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Cette semaine</span>
                        <span class="badge bg-info">{{ number_format($monActivite['patients_semaine']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Ce mois</span>
                        <span class="badge bg-success">{{ number_format($monActivite['patients_mois']) }}</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('receptionniste.rapports.historique') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-history me-1"></i> Voir mon historique
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <i class="fas fa-chart-line me-2 text-primary"></i> Évolution des enregistrements (7 jours)
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top médecins -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <i class="fas fa-trophy me-2 text-primary"></i> Top médecins consultés
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Médecin</th>
                                    <th>Consultations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topMedecins as $medecin)
                                <tr>
                                    <td>Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</small></td>
                                    <td><span class="badge bg-primary">{{ $medecin->consultations_effectuees_count }}</span></small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <i class="fas fa-file-alt me-2 text-primary"></i> Actions rapides
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <a href="{{ route('receptionniste.rapports.generer') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i> Générer un rapport
                        </a>
                        <a href="{{ route('receptionniste.rapports.historique') }}" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i> Voir mon historique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Patients enregistrés',
                data: {!! json_encode($chartData) !!},
                borderColor: '#2c6e9e',
                backgroundColor: 'rgba(44,110,158,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
@endsection