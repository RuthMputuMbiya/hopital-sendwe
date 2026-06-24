@extends('layouts.receptionniste')

@section('title', 'Aperçu du rapport')

@section('page-title', 'Aperçu du rapport')
@section('page-subtitle', $rapport['periode']['libelle'])

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i> Rapport {{ $rapport['type'] == 'general' ? 'général' : ($rapport['type'] == 'patients' ? 'des patients' : ($rapport['type'] == 'consultations' ? 'des consultations' : 'des examens')) }}
                </h5>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print me-1"></i> Imprimer
                    </button>
                    <a href="{{ route('receptionniste.rapports.export-pdf', request()->all()) }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf me-1"></i> Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- En-tête du rapport -->
            <div class="text-center mb-4 pb-3 border-bottom">
                <h3 class="text-primary">Hôpital Sendwe - Lubumbashi</h3>
                <h5>Rapport {{ $rapport['type'] == 'general' ? 'général' : ($rapport['type'] == 'patients' ? 'des patients' : ($rapport['type'] == 'consultations' ? 'des consultations' : 'des examens')) }}</h5>
                <p>Période : {{ $rapport['periode']['libelle'] }}</p>
                <p>Généré le : {{ $rapport['generated_at']->format('d/m/Y H:i') }} par {{ $rapport['generated_by']->nom_complet }}</p>
            </div>
            
            <!-- Statistiques patients -->
            @if(isset($rapport['patients']))
            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="fas fa-users me-2"></i> Patients</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="fs-2 fw-bold text-primary">{{ number_format($rapport['total_patients']) }}</div>
                            <small class="text-muted">Total patients</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="fs-2 fw-bold text-info">{{ number_format($rapport['patients_par_sexe']['hommes']) }}</div>
                            <small class="text-muted">Hommes</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="fs-2 fw-bold text-success">{{ number_format($rapport['patients_par_sexe']['femmes']) }}</div>
                            <small class="text-muted">Femmes</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Statistiques consultations -->
            @if(isset($rapport['consultations']))
            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="fas fa-stethoscope me-2"></i> Consultations</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="fs-2 fw-bold text-primary">{{ number_format($rapport['total_consultations']) }}</div>
                            <small class="text-muted">Total consultations</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Statistiques examens -->
            @if(isset($rapport['examens']))
            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="fas fa-flask me-2"></i> Examens</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="fs-2 fw-bold text-primary">{{ number_format($rapport['total_examens']) }}</div>
                            <small class="text-muted">Total examens</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Mon activité -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-primary"><i class="fas fa-user-circle me-2"></i> Mon activité sur la période</h6>
                <div class="bg-light p-2 rounded">
                    <i class="fas fa-user-plus text-primary me-1"></i>
                    Patients enregistrés : <strong>{{ number_format($rapport['mon_activite']) }}</strong>
                </div>
            </div>
            
            <!-- Pied de page -->
            <div class="text-center mt-4 pt-3 border-top">
                <small class="text-muted">Document généré automatiquement par le système de gestion médicale Hôpital Sendwe</small>
            </div>
        </div>
    </div>
</div>
@endsection