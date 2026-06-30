@extends('layouts.medecin')

@section('title', 'Détails consultation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-alt me-2" style="color: #1a4d6b;"></i>
                Détails de la consultation
            </h1>
            <p class="text-muted mb-0">{{ $consultation->numero_consultation ?? 'N/A' }}</p>
        </div>
        <div data-aos="fade-left">
            <div class="btn-group">
                <a href="{{ route('medecin.consultations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
                @if($consultation->statut_consultation != 'termine')
                <a href="{{ route('medecin.consultations.edit', $consultation->id_consultation) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> Modifier
                </a>
                @endif
                <a href="{{ route('medecin.bons-analyse.create', ['patientId' => $consultation->id_patient]) }}" 
                   class="btn btn-info">
                    <i class="fas fa-flask me-2"></i> Envoyer au laborantin
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne gauche -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            
            <!-- Informations patient -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Patient
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $consultation->patient->nom_complet ?? 'N/A' }}</h5>
                    <p class="text-muted mb-2">{{ $consultation->patient->numero_fiche ?? 'N/A' }}</p>
                    <hr>
                    <p class="mb-1"><i class="fas fa-calendar me-2 text-primary"></i> Né(e) le: {{ isset($consultation->patient->date_naissance) ? \Carbon\Carbon::parse($consultation->patient->date_naissance)->format('d/m/Y') : 'N/A' }}</p>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i> {{ $consultation->patient->telephone ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-location-dot me-2 text-primary"></i> {{ $consultation->patient->adresse ?? 'N/A' }}</p>
                </div>
            </div>
            
            <!-- Informations consultation -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-calendar me-2 text-primary"></i> Date: {{ isset($consultation->date_consultation) ? \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') : 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i> Heure: {{ $consultation->heure_consultation ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-user-md me-2 text-primary"></i> Médecin: Dr. {{ $consultation->medecin->nom_complet ?? 'N/A' }}</p>
                    @if($consultation->infirmier)
                    <p class="mb-2"><i class="fas fa-user-nurse me-2 text-primary"></i> Infirmier: {{ $consultation->infirmier->nom_complet ?? 'N/A' }}</p>
                    @endif
                    <p class="mb-0">
                        <i class="fas fa-flag-checkered me-2 text-primary"></i> Statut: 
                        {!! $consultation->statut_consultation_badge !!}
                    </p>
                </div>
            </div>
            
            <!-- Bilan Infirmier -->
            @if($consultation->bilan)
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-user-nurse me-2"></i> Bilan infirmier
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Poids</div>
                                <strong>{{ $consultation->bilan->poids ?? 'N/A' }} kg</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Taille</div>
                                <strong>{{ $consultation->bilan->taille ?? 'N/A' }} cm</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Température</div>
                                <strong>{{ $consultation->bilan->temperature ?? 'N/A' }} °C</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Tension</div>
                                <strong>{{ $consultation->bilan->tension_arterielle ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">FC</div>
                                <strong>{{ $consultation->bilan->frequence_cardiaque ?? 'N/A' }} bpm</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">FR</div>
                                <strong>{{ $consultation->bilan->frequence_respiratoire ?? 'N/A' }}/min</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">IMC</div>
                                <strong>{{ $consultation->bilan->imc ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">SpO2</div>
                                <strong>{{ $consultation->bilan->spo2 ?? 'N/A' }}%</strong>
                            </div>
                        </div>
                    </div>
                    @if($consultation->bilan->motif)
                    <hr>
                    <p class="mb-1"><strong>Motif:</strong> {{ $consultation->bilan->motif }}</p>
                    @endif
                    @if($consultation->bilan->symptomes)
                    <p class="mb-1"><strong>Symptômes:</strong> {{ $consultation->bilan->symptomes }}</p>
                    @endif
                    @if($consultation->bilan->observations)
                    <p class="mb-1"><strong>Observations:</strong> {{ $consultation->bilan->observations }}</p>
                    @endif
                    @if($consultation->bilan->allergies)
                    <p class="mb-1 text-danger"><strong>Allergies:</strong> {{ $consultation->bilan->allergies }}</p>
                    @endif
                    @if($consultation->bilan->traitements_en_cours)
                    <p class="mb-1"><strong>Traitements:</strong> {{ $consultation->bilan->traitements_en_cours }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <!-- Colonne droite -->
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <!-- Motif et Symptômes -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-question-circle me-2"></i> Motif
                        </div>
                        <div class="card-body">
                            <p>{{ $consultation->motif ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-head-side-medical me-2"></i> Symptômes
                        </div>
                        <div class="card-body">
                            <p>{{ $consultation->symptomes ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Diagnostic -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-2"></i> Diagnostic
                </div>
                <div class="card-body">
                    <p>{{ $consultation->diagnostic ?? 'N/A' }}</p>
                    @if($consultation->maladie)
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-disease me-2"></i>
                            <strong>Maladie associée:</strong> {{ $consultation->maladie->nom_maladie }}
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Prescription -->
            @if($consultation->prescription)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-prescription-bottle me-2"></i> Prescription
                </div>
                <div class="card-body">
                    <p>{{ $consultation->prescription }}</p>
                </div>
            </div>
            @endif
            
            <!-- Observations -->
            @if($consultation->observations_medecin)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-comment-dots me-2"></i> Observations
                </div>
                <div class="card-body">
                    <p>{{ $consultation->observations_medecin }}</p>
                </div>
            </div>
            @endif
            
            <!-- Bons d'analyse avec statuts -->
            @if($consultation->bonsAnalyse && $consultation->bonsAnalyse->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-flask me-2"></i> Bons d'analyse
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>N° Bon</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Priorité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultation->bonsAnalyse as $bon)
                                <tr>
                                    <td><code>{{ $bon->numero_bon }}</code></td>
                                    <td>{{ $bon->date_demande->format('d/m/Y') }}</td>
                                    <td>
                                        @if($bon->statut == 'termine')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Terminé
                                            </span>
                                        @elseif($bon->statut == 'retourne')
                                            <span class="badge bg-info">
                                                <i class="fas fa-undo me-1"></i> Retourné
                                            </span>
                                        @elseif($bon->statut == 'preleve')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-syringe me-1"></i> Prélevé
                                            </span>
                                        @elseif($bon->statut == 'analyse')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-microscope me-1"></i> En analyse
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : 'primary' }}">
                                            {{ $bon->priorite == 'urgent' ? 'Urgent' : 'Normal' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('medecin.bons-analyse.show', $bon->id_bon) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('medecin.bons-analyse.print', $bon->id_bon) }}" 
                                               class="btn btn-sm btn-outline-secondary" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Résultats si bon terminé -->
            @if($consultation->bonsAnalyse && $consultation->bonsAnalyse->where('statut', 'termine')->count() > 0)
            @foreach($consultation->bonsAnalyse->where('statut', 'termine') as $bon)
            @if($bon->resultats && count($bon->resultats) > 0)
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-file-medical me-2"></i> Résultats d'analyse - Bon N° {{ $bon->numero_bon }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Examen</th>
                                    <th>Résultat</th>
                                    <th>Commentaire</th>
                                    <th>Date saisie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bon->resultats as $key => $resultat)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $resultat['valeur'] ?? 'Non renseigné' }}</span>
                                    </td>
                                    <td>{{ $resultat['commentaire'] ?? '-' }}</td>
                                    <td>{{ isset($resultat['date_saisie']) ? \Carbon\Carbon::parse($resultat['date_saisie'])->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($bon->observations_laborantin)
                    <div class="mt-3">
                        <label class="fw-bold">Observations du laborantin :</label>
                        <p>{{ $bon->observations_laborantin }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        font-weight: 600;
    }
    .border-info {
        border-color: #0dcaf0 !important;
    }
    .border-success {
        border-color: #28a745 !important;
    }
</style>
@endpush
@endsection