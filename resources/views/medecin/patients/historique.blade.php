@extends('layouts.medecin')

@section('title', 'Historique médical - ' . $patient->nom_complet)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-history me-2" style="color: #1a4d6b;"></i>
                Historique médical complet
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-user-circle me-1"></i> {{ $patient->nom_complet }} 
                <span class="mx-2">•</span> 
                <i class="fas fa-qrcode me-1"></i> {{ $patient->numero_fiche }}
            </p>
        </div>
        <div data-aos="fade-left">
            <div class="btn-group">
                <a href="{{ route('medecin.patients.show', $patient->id_patient) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour dossier
                </a>
                <a href="{{ route('medecin.consultations.create', ['patientId' => $patient->id_patient]) }}" class="btn btn-primary">
                    <i class="fas fa-stethoscope me-2"></i> Nouvelle consultation
                </a>
                <button onclick="window.print()" class="btn btn-outline-success">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar patient -->
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="card sticky-top" style="top: 80px; z-index: 100;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 90px; height: 90px; background: linear-gradient(135deg, #1a4d6b, #2c6e9e); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-injured fa-3x text-white"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $patient->prenom }} {{ $patient->nom }}</h5>
                    <p class="text-muted small mb-2">{{ $patient->postnom }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $patient->numero_fiche }}</span>
                        @if($patient->sexe === 'M')
                            <span class="badge bg-info"><i class="fas fa-mars"></i> M</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-venus"></i> F</span>
                        @endif
                        <span class="badge bg-warning">{{ $patient->age }} ans</span>
                    </div>
                    
                    <hr>
                    
                    <div class="row small">
                        <div class="col-6">
                            <div class="border rounded p-2 mb-2">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                <div class="fw-bold">{{ $consultations->count() }}</div>
                                <small class="text-muted">Consultations</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 mb-2">
                                <i class="fas fa-microscope text-info"></i>
                                <div class="fw-bold">{{ $examens->count() }}</div>
                                <small class="text-muted">Examens</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-start small">
                        <p class="mb-1">
                            <i class="fas fa-phone me-2 text-primary"></i> 
                            <strong>Tél:</strong> {{ $patient->telephone }}
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-location-dot me-2 text-primary"></i> 
                            <strong>Adresse:</strong> {{ Str::limit($patient->adresse, 35) }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-2 text-primary"></i> 
                            <strong>Inscrit:</strong> {{ $patient->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-light small mb-0">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        {{ $patient->antecedents_medicaux ? Str::limit($patient->antecedents_medicaux, 50) : 'Aucun antécédent' }}
                    </div>
                    
                    @if($patient->allergies)
                    <div class="alert alert-warning small mb-0 mt-2">
                        <i class="fas fa-allergies me-1"></i>
                        Allergies: {{ Str::limit($patient->allergies, 50) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Contenu principal - Chronologie -->
        <div class="col-md-9" data-aos="fade-up" data-aos-delay="200">
            
            <!-- Filtres de période -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-auto">
                            <button class="btn btn-outline-primary filter-btn active" data-filter="all">
                                <i class="fas fa-list me-1"></i> Tout
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary filter-btn" data-filter="consultation">
                                <i class="fas fa-stethoscope me-1"></i> Consultations
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary filter-btn" data-filter="examen">
                                <i class="fas fa-microscope me-1"></i> Examens
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" id="exportPdfBtn">
                                <i class="fas fa-file-pdf me-1"></i> Exporter PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques médicales -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-disease me-2" style="color: #1a4d6b;"></i>
                            Maladies les plus fréquentes
                        </div>
                        <div class="card-body">
                            @forelse($medicalStats['maladies_frequentes'] ?? [] as $maladie)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <i class="fas fa-virus me-2 text-primary"></i>
                                    <strong>{{ $maladie->maladie->nom_maladie ?? 'Non spécifiée' }}</strong>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-primary">{{ $maladie->total }} fois</span>
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ ($maladie->total / max($consultations->count(), 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-3">
                                <i class="fas fa-chart-line fa-2x text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Aucune maladie enregistrée</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2" style="color: #1a4d6b;"></i>
                            Consultations par année
                        </div>
                        <div class="card-body">
                            @forelse($medicalStats['consultations_par_annee'] ?? [] as $annee)
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 70px;">
                                    <strong>{{ $annee->annee }}</strong>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 30px; border-radius: 10px;">
                                        <div class="progress-bar bg-primary d-flex align-items-center justify-content-end px-2" 
                                             style="width: {{ min(($annee->total / max($consultations->count(), 1)) * 100, 100) }}%;">
                                            <span class="text-white fw-bold">{{ $annee->total }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-3">
                                <i class="fas fa-chart-simple fa-2x text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Aucune donnée</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Types d'examens -->
            @if(isset($medicalStats['examens_par_type']) && $medicalStats['examens_par_type']->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2" style="color: #1a4d6b;"></i>
                    Répartition des examens par type
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="examensTypeChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            @foreach($medicalStats['examens_par_type'] as $type)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $type->type_examen }}</span>
                                <span class="badge bg-info">{{ $type->total }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Chronologie timeline -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-timeline me-2" style="color: #1a4d6b;"></i>
                    Ligne du temps médicale
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($evenements ?? [] as $event)
                        <div class="timeline-item mb-4" data-type="{{ $event['type'] }}">
                            <div class="timeline-badge {{ $event['type'] == 'consultation' ? 'bg-primary' : 'bg-info' }}">
                                <i class="fas {{ $event['type'] == 'consultation' ? 'fa-stethoscope' : 'fa-microscope' }}"></i>
                            </div>
                            <div class="timeline-content card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <h5 class="mb-0">
                                                    {{ $event['titre'] }}
                                                    @if($event['type'] == 'examen' && isset($event['statut']))
                                                        <span class="badge {{ $event['statut'] == 'termine' ? 'bg-success' : 'bg-warning' }} ms-2">
                                                            {{ $event['statut'] == 'termine' ? 'Terminé' : 'En attente' }}
                                                        </span>
                                                    @endif
                                                </h5>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3 mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i> 
                                                    {{ $event['date']->format('d/m/Y') }}
                                                    <i class="fas fa-clock ms-2 me-1"></i>
                                                    {{ $event['date']->format('H:i') }}
                                                </small>
                                                @if($event['type'] == 'consultation')
                                                    <small class="text-muted">
                                                        <i class="fas fa-user-md me-1"></i> 
                                                        Dr. {{ $event['medecin'] }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        <i class="fas fa-user-nurse me-1"></i> 
                                                        {{ $event['laborantin'] }}
                                                    </small>
                                                @endif
                                            </div>
                                            <p class="mb-2">{{ Str::limit($event['description'], 150) }}</p>
                                            
                                            @if($event['type'] == 'consultation')
                                                <div class="row small text-muted mt-2">
                                                    @if($event['details']->tension_arterielle)
                                                    <div class="col-auto">
                                                        <i class="fas fa-heartbeat me-1 text-danger"></i>
                                                        Tension: {{ $event['details']->tension_arterielle }}
                                                    </div>
                                                    @endif
                                                    @if($event['details']->temperature)
                                                    <div class="col-auto">
                                                        <i class="fas fa-thermometer-half me-1 text-warning"></i>
                                                        Temp: {{ $event['details']->temperature }}°C
                                                    </div>
                                                    @endif
                                                    @if($event['details']->poids)
                                                    <div class="col-auto">
                                                        <i class="fas fa-weight-scale me-1 text-success"></i>
                                                        Poids: {{ $event['details']->poids }} kg
                                                    </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            @if($event['type'] == 'consultation')
                                                <a href="{{ route('medecin.consultations.show', $event['details']->id_consultation) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Voir le détail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($event['type'] == 'consultation' && $event['details']->prescription)
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <i class="fas fa-prescription-bottle me-1 text-success"></i>
                                        <strong>Prescription:</strong> {{ Str::limit($event['details']->prescription, 100) }}
                                    </div>
                                    @endif
                                    
                                    @if($event['type'] == 'examen' && $event['details']->resultat)
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <i class="fas fa-flask me-1 text-info"></i>
                                        <strong>Résultat:</strong> {{ Str::limit($event['details']->resultat, 100) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-4x text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Aucun événement médical enregistré</h5>
                            <p class="text-muted">Commencez par créer une consultation pour ce patient.</p>
                            <a href="{{ route('medecin.consultations.create', ['patientId' => $patient->id_patient]) }}" class="btn btn-primary mt-2">
                                <i class="fas fa-stethoscope me-2"></i> Première consultation
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #1a4d6b, #d4af37, #1a4d6b);
    }
    .timeline-item {
        position: relative;
    }
    .timeline-badge {
        position: absolute;
        left: -40px;
        top: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .timeline-badge.bg-primary { background: linear-gradient(135deg, #1a4d6b, #2c6e9e); }
    .timeline-badge.bg-info { background: linear-gradient(135deg, #17a2b8, #5dade2); }
    .timeline-content {
        margin-left: 0;
        transition: transform 0.2s;
    }
    .timeline-content:hover {
        transform: translateX(5px);
    }
    .timeline-item {
        opacity: 0;
        animation: fadeInUp 0.5s ease forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .timeline-item:nth-child(5) { animation-delay: 0.5s; }
    .timeline-item:nth-child(6) { animation-delay: 0.6s; }
    .timeline-item.hidden { display: none; }
    .filter-btn.active { background: #1a4d6b; color: white; }
    
    @media print {
        .sidebar, .btn, .navbar, .filter-btn, #exportPdfBtn, .sticky-top { display: none !important; }
        .col-md-3, .col-md-9 { width: 100%; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des types d'examens
    @if(isset($medicalStats['examens_par_type']) && $medicalStats['examens_par_type']->count() > 0)
    new Chart(document.getElementById('examensTypeChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($medicalStats['examens_par_type']->pluck('type_examen')) !!},
            datasets: [{
                data: {!! json_encode($medicalStats['examens_par_type']->pluck('total')) !!},
                backgroundColor: ['#1a4d8c', '#2c6e9e', '#3498db', '#5dade2', '#85c1e9', '#aed6f1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    @endif
    
    // Filtrage par type
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.timeline-item').forEach(item => {
                if (filter === 'all' || item.dataset.type === filter) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush
@endsection