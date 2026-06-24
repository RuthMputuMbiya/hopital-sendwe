@extends('layouts.receptionniste')

@section('title', 'Tableau des enregistrements')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-archive me-2" style="color: var(--secondary);"></i>
                Tableau des enregistrements
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-line me-1"></i> Suivi des activités d'enregistrement
            </p>
        </div>
        <div data-aos="fade-left">
            <div class="btn-group">
                <a href="{{ route('receptionniste.enregistrements.historique') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history me-2"></i> Historique
                </a>
                <a href="{{ route('receptionniste.enregistrements.rapport') }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar me-2"></i> Rapport
                </a>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['total_enregistrements']) }}</div>
                <div class="stat-label">Total enregistrements</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ number_format($stats['enregistrements_aujourdhui']) }}</div>
                <div class="stat-label">Aujourd'hui</div>
                <div class="stat-trend {{ $stats['enregistrements_aujourdhui'] > $stats['enregistrements_hier'] ? 'up' : 'down' }}">
                    <i class="fas fa-arrow-{{ $stats['enregistrements_aujourdhui'] > $stats['enregistrements_hier'] ? 'up' : 'down' }} me-1"></i>
                    vs hier
                </div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-number">{{ number_format($stats['enregistrements_semaine']) }}</div>
                <div class="stat-label">Cette semaine</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number">{{ number_format($stats['enregistrements_mois']) }}</div>
                <div class="stat-label">Ce mois</div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="row g-4 mb-4">
        <div class="col-md-8" data-aos="fade-right">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>
                    Évolution des enregistrements (7 derniers jours)
                </div>
                <div class="card-body">
                    <canvas id="enregistrementsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2" style="color: var(--secondary);"></i>
                    Top 5 jours d'enregistrement
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Enregistrements</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topJours as $jour)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($jour->date)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-primary">{{ $jour->total }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers enregistrements -->
    <div class="row g-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock me-2" style="color: var(--secondary);"></i>
                    Derniers enregistrements
                    <span class="badge bg-primary ms-2">{{ $recentEnregistrements->count() }} récents</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>N° Fiche</th>
                                    <th>Patient</th>
                                    <th>Téléphone</th>
                                    <th>Enregistré par</th>
                                    <th>Date/Heure</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnregistrements as $patient)
                                <tr>
                                    <td><code>{{ $patient->numero_fiche }}</code></td>
                                    <td><strong>{{ $patient->nom_complet }}</strong></td>
                                    <td>{{ $patient->telephone }}</td>
                                    <td>{{ Auth::user()->nom_complet }}</td>
                                    <td>
                                        {{ $patient->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $patient->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('receptionniste.patients.show', $patient->id_patient) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-5">Aucun enregistrement</td></tr>
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
    new Chart(document.getElementById('enregistrementsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Nombre d\'enregistrements',
                data: {!! json_encode($chartData) !!},
                borderColor: '#2c6e9e',
                backgroundColor: 'rgba(44, 110, 158, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#2c6e9e',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endpush
@endsection