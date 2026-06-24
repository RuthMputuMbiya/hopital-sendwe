@extends('layouts.medecin')

@section('title', 'Détails maladie')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-disease me-2" style="color: var(--secondary);"></i>
                Détails de la maladie
            </h1>
            <p class="text-muted mb-0">{{ $maladie->nom_maladie }} ({{ $maladie->code_maladie }})</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.maladies.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('medecin.maladies.edit', $maladie->id_maladie) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-virus fa-3x text-white"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $maladie->nom_maladie }}</h4>
                    <p class="text-muted mb-2">{{ $maladie->code_maladie }}</p>
                    
                    <div class="mb-3">
                        @if($maladie->categorie)
                            <span class="badge badge-info">{{ $maladie->categorie }}</span>
                        @endif
                        <span class="badge bg-primary">{{ $maladie->consultations_count }} consultations</span>
                    </div>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p><strong><i class="fas fa-calendar-alt me-2 text-primary"></i> Créé le:</strong> {{ $maladie->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong><i class="fas fa-edit me-2 text-primary"></i> Modifié le:</strong> {{ $maladie->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-align-left me-2"></i> Description
                </div>
                <div class="card-body">
                    <p>{{ $maladie->description }}</p>
                </div>
            </div>
            
            @if($maladie->symptomes)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-head-side-medical me-2"></i> Symptômes
                </div>
                <div class="card-body">
                    <p>{{ $maladie->symptomes }}</p>
                </div>
            </div>
            @endif
            
            <!-- Graphique des consultations -->
            @if($consultationsParMois->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Évolution des consultations (6 derniers mois)
                </div>
                <div class="card-body">
                    <canvas id="consultationsChart" height="250"></canvas>
                </div>
            </div>
            @endif
            
            <!-- Dernières consultations -->
            @if($dernieresConsultations->count() > 0)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-2"></i> Dernières consultations
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Médecin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dernieresConsultations as $consultation)
                                <tr>
                                    <td>{{ $consultation->date_consultation->format('d/m/Y') }}</small></td>
                                    <td>{{ $consultation->patient->nom_complet ?? 'N/A' }}<br><small>{{ $consultation->patient->numero_fiche ?? '' }}</small></td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($consultationsParMois->count() > 0)
    const moisLabels = {!! json_encode($consultationsParMois->map(function($item) { 
        return date('F Y', mktime(0,0,0,$item->mois,1,$item->annee)); 
    })) !!};
    const consultationsData = {!! json_encode($consultationsParMois->pluck('total')->reverse()) !!};
    
    new Chart(document.getElementById('consultationsChart'), {
        type: 'line',
        data: {
            labels: moisLabels.reverse(),
            datasets: [{
                label: 'Nombre de consultations',
                data: consultationsData,
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
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Nombre de consultations' } } }
        }
    });
    @endif
</script>
@endpush
@endsection