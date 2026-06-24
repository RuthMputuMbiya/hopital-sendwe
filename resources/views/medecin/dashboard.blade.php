@extends('layouts.medecin')

@section('title', 'Mon Cabinet - Médecin')

@section('page-title', 'Mon Cabinet Médical')
@section('page-subtitle', 'Gestion de vos consultations et patients')

@section('content')
<div class="container-fluid">
    <!-- Cartes Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total_consultations'] ?? 0) }}</div>
                <div class="stat-label">Consultations totales</div>
                <div class="stat-trend up">
                    <i class="fas fa-calendar me-1"></i> {{ $stats['consultations_mois'] ?? 0 }} ce mois
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['patients_vus'] ?? 0) }}</div>
                <div class="stat-label">Patients vus</div>
                <div class="stat-trend up">
                    <i class="fas fa-user-plus me-1"></i> {{ $stats['nouveaux_patients'] ?? 0 }} nouveaux
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">{{ number_format($stats['consultations_aujourdhui'] ?? 0) }}</div>
                <div class="stat-label">Consultations aujourd'hui</div>
                <div class="stat-trend stable">
                    <i class="fas fa-hourglass-half me-1"></i> En attente: {{ $stats['en_attente'] ?? 0 }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number">{{ number_format($stats['moyenne_consultations'] ?? 0, 1) }}</div>
                <div class="stat-label">Moyenne par jour</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up me-1"></i> Ce mois
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8" data-aos="fade-right">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>
                    Évolution des consultations (6 derniers mois)
                </div>
                <div class="card-body">
                    <canvas id="consultationsChart" height="320"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4" data-aos="fade-left">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2" style="color: var(--secondary);"></i>
                    Répartition par maladie
                </div>
                <div class="card-body">
                    <canvas id="maladiesChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients en attente -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-hourglass-half me-2" style="color: var(--secondary);"></i>
                    Patients en attente de consultation
                    <span class="badge bg-warning ms-2">{{ $patientsEnAttente->count() ?? 0 }} en attente</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Heure</th>
                                    <th>Patient</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientsEnAttente ?? [] as $consultation)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ \Carbon\Carbon::parse($consultation->heure_consultation)->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $consultation->patient->numero_fiche ?? '' }}</small>
                                    </td>
                                    <td>{{ Str::limit($consultation->motif, 40) }}</td>
                                    <td><span class="badge badge-warning">En cours</span></td>
                                    <td>
                                        <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-stethoscope"></i> Consulter
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Aucun patient en attente</small></td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières consultations -->
    <div class="row g-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-2" style="color: var(--secondary);"></i>
                    Mes dernières consultations
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Diagnostic</th>
                                    <th>Prescription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentConsultations ?? [] as $consultation)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($consultation->heure_consultation)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ Str::limit($consultation->diagnostic, 50) }}</small></td>
                                    <td>{{ Str::limit($consultation->prescription, 40) ?? '-' }}</small></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('medecin.consultations.edit', $consultation->id_consultation) }}" 
                                               class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <td><td colspan="5" class="text-center py-4">Aucune consultation récente</small></td>
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
    // Graphique des consultations
    const ctx = document.getElementById('consultationsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin']) !!},
            datasets: [{
                label: 'Nombre de consultations',
                data: {!! json_encode($chartData ?? [0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#1a4d8c',
                backgroundColor: 'rgba(26, 77, 140, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#1a4d8c',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: function(context) { return 'Consultations: ' + context.raw; } } }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Nombre de consultations', font: { weight: 'bold' } } },
                x: { title: { display: true, text: 'Mois', font: { weight: 'bold' } }, grid: { display: false } }
            }
        }
    });
    
    // Graphique des maladies (Pie Chart)
    const maladiesCtx = document.getElementById('maladiesChart').getContext('2d');
    new Chart(maladiesCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($maladiesLabels ?? []) !!},
            datasets: [{
                data: {!! json_encode($maladiesData ?? []) !!},
                backgroundColor: ['#1a4d8c', '#2c6e9e', '#3498db', '#5dade2', '#85c1e9', '#aed6f1', '#d4af37'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} cas (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection