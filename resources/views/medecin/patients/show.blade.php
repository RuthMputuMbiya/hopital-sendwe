@extends('layouts.medecin')

@section('title', 'Dossier patient')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-user-circle me-2" style="color: var(--secondary);"></i>
                Dossier médical
            </h1>
            <p class="text-muted mb-0">{{ $patient->nom_complet }} ({{ $patient->numero_fiche }})</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.patients.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('medecin.consultations.create', ['patient_id' => $patient->id_patient]) }}" class="btn btn-primary">
                <i class="fas fa-stethoscope me-2"></i> Nouvelle consultation
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Carte informations patient -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-injured fa-3x text-white"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $patient->prenom }} {{ $patient->nom }}</h4>
                    <p class="text-muted mb-3">{{ $patient->postnom }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $patient->numero_fiche }}</span>
                        @if($patient->sexe === 'M')
                            <span class="badge badge-info"><i class="fas fa-mars"></i> Masculin</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-venus"></i> Féminin</span>
                        @endif
                        @if($patient->groupe_sanguin)
                            <span class="badge badge-warning">{{ $patient->groupe_sanguin }}</span>
                        @endif
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Âge</small>
                                <strong>{{ $patient->age }} ans</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Né(e) le</small>
                                <strong>{{ $patient->date_naissance->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact et informations -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-address-card me-2"></i> Coordonnées
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i> 
                        <strong>Téléphone:</strong> {{ $patient->telephone }}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-location-dot me-2 text-primary"></i> 
                        <strong>Adresse:</strong> {{ $patient->adresse }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2 text-primary"></i> 
                        <strong>Inscrit le:</strong> {{ $patient->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            <!-- Statistiques patient -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Statistiques
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Consultations totales</span>
                        <span class="badge bg-primary">{{ number_format($patientStats['total_consultations']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Examens réalisés</span>
                        <span class="badge bg-info">{{ number_format($patientStats['total_examens']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dernière visite</span>
                        <span class="badge {{ $patientStats['derniere_visite'] != 'Aucune' ? 'badge-success' : 'badge-warning' }}">
                            {{ $patientStats['derniere_visite'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations médicales détaillées -->
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <!-- Antécédents et allergies -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-notes-medical me-2"></i> Informations médicales
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary"><i class="fas fa-history me-1"></i> Antécédents médicaux</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $patient->antecedents_medicaux ?? 'Aucun antécédent renseigné' }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary"><i class="fas fa-allergies me-1"></i> Allergies</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $patient->allergies ?? 'Aucune allergie renseignée' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dernière consultation -->
            @if($derniereConsultation)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-2"></i> Dernière consultation
                    <span class="badge bg-success ms-2">{{ $derniereConsultation->date_consultation->format('d/m/Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user-md me-1"></i> Médecin:</strong> {{ $derniereConsultation->medecin->nom_complet ?? 'N/A' }}</p>
                            <p><strong><i class="fas fa-disease me-1"></i> Diagnostic:</strong> {{ $derniereConsultation->diagnostic }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-prescription-bottle me-1"></i> Prescription:</strong> {{ $derniereConsultation->prescription ?? 'Aucune' }}</p>
                            <p><strong><i class="fas fa-chart-line me-1"></i> Observations:</strong> {{ $derniereConsultation->observations_medecin ?? 'Aucune' }}</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('medecin.consultations.show', $derniereConsultation->id_consultation) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Voir le détail
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Prochain examen -->
            @if($prochainExamen)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-microscope me-2"></i> Prochain examen
                    <span class="badge bg-warning ms-2">{{ $prochainExamen->date_examen->format('d/m/Y') }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Type:</strong> {{ $prochainExamen->type_examen }}</p>
                    <p><strong>Statut:</strong> 
                        @if($prochainExamen->statut == 'en_attente')
                            <span class="badge badge-warning">En attente</span>
                        @elseif($prochainExamen->statut == 'termine')
                            <span class="badge badge-success">Terminé</span>
                        @else
                            <span class="badge badge-danger">Annulé</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif
            
            <!-- Historique des consultations -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i> Historique des consultations
                    <span class="badge bg-primary ms-2">{{ $consultations->total() }} consultations</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Médecin</th>
                                    <th>Diagnostic</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultations as $consultation)
                                <tr>
                                    <td>
                                        {{ $consultation->date_consultation->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $consultation->heure_consultation }}</small>
                                    </td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($consultation->diagnostic, 50) }}</td>
                                    <td>
                                        <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </table>
                                @empty
                                <tr><td colspan="4" class="text-center py-4">Aucune consultation</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $consultations->links() }}
                </div>
            </div>
            
            <!-- Historique des examens -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-microscope me-2"></i> Historique des examens
                    <span class="badge bg-info ms-2">{{ $examens->total() }} examens</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Laborantin</th>
                                    <th>Résultat</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examens as $examen)
                                <tr>
                                    <td>
                                        {{ $examen->date_examen->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $examen->heure_examen }}</small>
                                    </td>
                                    <td>{{ $examen->type_examen }}</td>
                                    <td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($examen->resultat ?? 'En attente', 40) }}</td>
                                    <td>
                                        @if($examen->statut == 'termine')
                                            <span class="badge badge-success">Terminé</span>
                                        @elseif($examen->statut == 'en_attente')
                                            <span class="badge badge-warning">En attente</span>
                                        @else
                                            <span class="badge badge-danger">Annulé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">Aucun examen</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $examens->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection