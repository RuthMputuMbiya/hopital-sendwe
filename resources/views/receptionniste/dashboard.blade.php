@extends('layouts.receptionniste')

@section('title', 'Accueil - Réception')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec date en français et informations utilisateur -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-desktop me-2" style="color: var(--secondary);"></i>
                Accueil - Réception
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} | 
                <i class="fas fa-clock ms-2 me-1"></i> 
                {{ now()->format('H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2" data-aos="fade-left">
            <!-- Informations utilisateur -->
            <div class="d-flex align-items-center me-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                    <i class="fas fa-user-circle text-primary fa-lg"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                    <br>
                    <small class="text-muted">Réceptionniste</small>
                </div>
            </div>
            
            <!-- Bouton déconnexion -->
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>

    <!-- Cartes Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total_patients'] ?? 0) }}</div>
                <div class="stat-label">Patients enregistrés</div>
                <div class="stat-trend up"><i class="fas fa-chart-line me-1"></i> Total général</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                <div class="stat-number">{{ number_format($stats['patients_aujourdhui'] ?? 0) }}</div>
                <div class="stat-label">Nouveaux aujourd'hui</div>
                <div class="stat-trend up">
                    <i class="fas fa-calendar me-1"></i> 
                    @if(($stats['patients_hier'] ?? 0) > 0)
                        +{{ number_format((($stats['patients_aujourdhui'] ?? 0) - ($stats['patients_hier'] ?? 0)) / max(($stats['patients_hier'] ?? 0), 1) * 100, 1) }}% vs hier
                    @else
                        Premier enregistrement
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-number">{{ number_format($stats['patients_semaine'] ?? 0) }}</div>
                <div class="stat-label">Cette semaine</div>
                <div class="stat-trend stable">
                    <i class="fas fa-chart-line me-1"></i> 
                    {{ number_format($stats['patients_mois'] ?? 0) }} ce mois
                </div>
            </div>
        </div>
    </div>

    <!-- Recherche rapide -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-search me-2" style="color: var(--secondary);"></i>
                    Recherche rapide d'un patient
                </div>
                <div class="card-body">
                    <form action="{{ route('receptionniste.patients.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" 
                                           placeholder="Rechercher par nom, prénom, matricule ou numéro de fiche..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'activité -->
    <div class="row g-4 mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>
                    Activité d'enregistrement des patients (7 derniers jours)
                </div>
                <div class="card-body">
                    <canvas id="patientsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers patients enregistrés -->
    <div class="row g-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock me-2" style="color: var(--secondary);"></i>
                    Derniers patients enregistrés
                    <span class="badge bg-primary ms-2">{{ $recentPatients->count() ?? 0 }} nouveaux</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Numéro fiche</th>
                                    <th>Nom complet</th>
                                    <th>Sexe</th>
                                    <th>Téléphone</th>
                                    <th>Date d'enregistrement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPatients ?? [] as $patient)
                                <tr>
                                    <td><code>{{ $patient->numero_fiche }}</code></td>
                                    <td>
                                        <strong>{{ $patient->nom_complet }}</strong><br>
                                        <small class="text-muted">Né(e) le {{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</small>
                                    </td>
                                    <td>{{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}</td>
                                    <td>{{ $patient->telephone }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($patient->created_at)->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($patient->created_at)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('receptionniste.patients.show', $patient->id_patient) }}" 
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('receptionniste.patients.edit', $patient->id_patient) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-notes-medical fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Aucun patient enregistré</p>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('patientsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']) !!},
            datasets: [{
                label: 'Patients enregistrés',
                data: {!! json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#2c6e9e',
                backgroundColor: 'rgba(44, 110, 158, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#2c6e9e',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: function(context) { return 'Patients: ' + context.raw; } } }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Nombre de patients', font: { weight: 'bold' } }, ticks: { stepSize: 1 } },
                x: { title: { display: true, text: 'Jours de la semaine', font: { weight: 'bold' } }, grid: { display: false } }
            }
        }
    });
</script>
@endpush
@endsection