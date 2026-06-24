@extends('layouts.receptionniste')

@section('title', 'Dossier patient - ' . $patient->nom_complet)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-user-circle me-2" style="color: var(--secondary);"></i>
                Dossier patient
            </h1>
            <p class="text-muted mb-0">
                <strong>{{ $patient->nom_complet }}</strong> ({{ $patient->numero_fiche }})
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('receptionniste.patients.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('receptionniste.patients.edit', $patient->id_patient) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Modifier
            </a>
        </div>
    </div>

    <!-- Informations du patient -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="user-avatar-lg mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, #1a4d8c, #2c6e9e); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                        <h5 class="mt-3 mb-0">{{ $patient->nom_complet }}</h5>
                        <p class="text-muted small">{{ $patient->numero_fiche }}</p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Sexe</label>
                            <span>{{ $patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Âge</label>
                            <span>{{ $patient->age }} ans</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block">Date naissance</label>
                            <span>{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block">Téléphone</label>
                            <span>{{ $patient->telephone }}</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block">Email</label>
                            <span>{{ $patient->email ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block">Adresse</label>
                            <span>{{ $patient->adresse }}</span>
                        </div>
                        @if($patient->groupe_sanguin)
                        <div class="col-12">
                            <label class="text-muted small d-block">Groupe sanguin</label>
                            <span><strong>{{ $patient->groupe_sanguin }}</strong></span>
                        </div>
                        @endif
                    </div>

                    @if($medecinTraitant)
                    <div class="mt-3 pt-3 border-top">
                        <label class="text-muted small d-block">Médecin traitant</label>
                        <span class="badge badge-info">{{ $medecinTraitant->nom_complet }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-4">
                    <div class="stat-card text-center">
                        <div class="stat-number">{{ number_format($stats['total_consultations']) }}</div>
                        <div class="stat-label">Consultations</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card text-center">
                        <div class="stat-number">{{ number_format($stats['total_examens']) }}</div>
                        <div class="stat-label">Examens</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card text-center">
                        <div class="stat-number">{{ number_format($stats['total_bons_analyse']) }}</div>
                        <div class="stat-label">Bons d'analyse</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="patientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="consultations-tab" data-bs-toggle="tab" data-bs-target="#consultations" type="button" role="tab">
                        <i class="fas fa-stethoscope me-1"></i> Consultations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="examens-tab" data-bs-toggle="tab" data-bs-target="#examens" type="button" role="tab">
                        <i class="fas fa-microscope me-1"></i> Examens
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bons-tab" data-bs-toggle="tab" data-bs-target="#bons" type="button" role="tab">
                        <i class="fas fa-file-prescription me-1"></i> Bons d'analyse
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="patientTabsContent">
                <!-- Tab Consultations -->
                <div class="tab-pane fade show active" id="consultations" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Médecin</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultations as $consultation)
                                <tr>
                                    <td>{{ $consultation->date_consultation_formatee }}</td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($consultation->motif ?? '', 50) }}</td>
                                    <td>{!! $consultation->statut_badge !!}</td>
                                    <td>
                                        <a href="{{ route('receptionniste.examens.show', $consultation->id_consultation) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Aucune consultation</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $consultations->links() }}
                </div>

                <!-- Tab Examens -->
                <div class="tab-pane fade" id="examens" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Résultat</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examens as $examen)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($examen->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $examen->type ?? 'N/A' }}</td>
                                    <td>
                                        @if($examen->statut == 'termine')
                                            <span class="badge badge-success">Terminé</span>
                                        @elseif($examen->statut == 'en_attente')
                                            <span class="badge badge-warning">En attente</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $examen->statut }}</span>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($examen->resultat ?? '', 30) }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Aucun examen</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $examens->links() }}
                </div>

                <!-- Tab Bons d'analyse -->
                <div class="tab-pane fade" id="bons" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Bon</th>
                                    <th>Date</th>
                                    <th>Médecin</th>
                                    <th>Examen demandé</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bonsAnalyse as $bon)
                                <tr>
                                    <td><code>{{ $bon->numero_bon ?? 'N/A' }}</code></td>
                                    <td>{{ \Carbon\Carbon::parse($bon->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $bon->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($bon->examen_demande ?? '', 30) }}</td>
                                    <td>
                                        @if($bon->statut == 'termine')
                                            <span class="badge badge-success">Terminé</span>
                                        @elseif($bon->statut == 'en_attente')
                                            <span class="badge badge-warning">En attente</span>
                                        @elseif($bon->statut == 'preleve')
                                            <span class="badge badge-info">Prélevé</span>
                                        @elseif($bon->statut == 'analyse')
                                            <span class="badge badge-primary">En analyse</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $bon->statut }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- ✅ Utiliser la route receptionniste.bons-analyse.show -->
                                        @if(Route::has('receptionniste.bons-analyse.show'))
                                        <a href="{{ route('receptionniste.bons-analyse.show', $bon->id_bon) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @else
                                        <a href="#" 
                                           class="btn btn-sm btn-outline-primary"
                                           onclick="alert('Route non configurée. Veuillez ajouter la route receptionniste.bons-analyse.show')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        
                                        @if($bon->statut == 'termine')
                                            @if(Route::has('receptionniste.bons-analyse.print'))
                                            <a href="{{ route('receptionniste.bons-analyse.print', $bon->id_bon) }}" 
                                               class="btn btn-sm btn-outline-success" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @else
                                            <a href="#" 
                                               class="btn btn-sm btn-outline-success"
                                               onclick="alert('Route non configurée. Veuillez ajouter la route receptionniste.bons-analyse.print')">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">Aucun bon d'analyse</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $bonsAnalyse->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1a4d8c;
    }
    .stat-label {
        font-size: 13px;
        color: #6c757d;
    }
    .badge-info {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 10px 20px;
    }
    .nav-tabs .nav-link.active {
        color: #1a4d8c;
        border-bottom: 3px solid #1a4d8c;
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        color: #1a4d8c;
    }
</style>
@endpush