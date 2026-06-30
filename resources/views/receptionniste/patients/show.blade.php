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
                    
                    <!-- Statut du patient -->
                    <div class="alert alert-{{ $patient->statut == 'en_attente_infirmier' ? 'warning' : ($patient->statut == 'bilan_fait' ? 'info' : ($patient->statut == 'en_consultation' ? 'primary' : 'success')) }} mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-{{ $patient->statut == 'en_attente_infirmier' ? 'clock' : ($patient->statut == 'bilan_fait' ? 'check-circle' : ($patient->statut == 'en_consultation' ? 'stethoscope' : 'check-circle')) }} me-2"></i>
                            <div>
                                <strong>Statut :</strong> {!! $patient->statut_badge !!}
                                @if($patient->statut == 'en_attente_infirmier')
                                    <br><small class="text-muted">En attente de bilan infirmier</small>
                                @elseif($patient->statut == 'bilan_fait')
                                    <br><small class="text-muted">Bilan fait, en attente du médecin</small>
                                @elseif($patient->statut == 'en_consultation')
                                    <br><small class="text-muted">En consultation avec le médecin</small>
                                @elseif($patient->statut == 'termine')
                                    <br><small class="text-muted">Consultation terminée</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Sexe</label>
                            <span>{{ $patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Âge</label>
                            <span>
                                @if($patient->age)
                                    <strong>{{ $patient->age }} ans</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
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
                    </div>

                    @if($medecinTraitant)
                    <div class="mt-3 pt-3 border-top">
                        <label class="text-muted small d-block">Médecin traitant</label>
                        <span class="badge bg-info">{{ $medecinTraitant->nom_complet }}</span>
                    </div>
                    @endif

                    @if(isset($consultation) && $consultation->infirmier)
                    <div class="mt-2">
                        <label class="text-muted small d-block">Infirmier assigné</label>
                        <span class="badge bg-warning text-dark">{{ $consultation->infirmier->nom_complet }}</span>
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
                
                <!-- TAB CONSULTATIONS -->
                <div class="tab-pane fade show active" id="consultations" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Médecin</th>
                                    <th>Infirmier</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultations as $consultation)
                                <tr>
                                    <td>{{ $consultation->date_consultation_formatee }}</td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ $consultation->infirmier->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($consultation->motif ?? '', 50) }}</td>
                                    <td>{!! $consultation->statut_consultation_badge !!}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                        Aucune consultation
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $consultations->links() }}
                </div>

                <!-- TAB EXAMENS -->
                <div class="tab-pane fade" id="examens" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examens as $examen)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($examen->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $examen->type ?? 'N/A' }}</td>
                                    <td>
                                        @if($examen->statut == 'termine')
                                            <span class="badge bg-success">Terminé</span>
                                        @elseif($examen->statut == 'en_attente')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $examen->statut }}</span>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($examen->resultat ?? '', 30) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-microscope fa-2x d-block mb-2"></i>
                                        Aucun examen
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $examens->links() }}
                </div>

                <!-- TAB BONS D'ANALYSE -->
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
                                            <span class="badge bg-success">Terminé</span>
                                        @elseif($bon->statut == 'en_attente')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @elseif($bon->statut == 'preleve')
                                            <span class="badge bg-info">Prélevé</span>
                                        @elseif($bon->statut == 'analyse')
                                            <span class="badge bg-primary">En analyse</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $bon->statut }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-prescription fa-2x d-block mb-2"></i>
                                        Aucun bon d'analyse
                                    </td>
                                </tr>
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
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
    .badge-warning {
        background-color: #fff3e0;
        color: #f39c12;
    }
    .badge-success {
        background-color: #e6f7ed;
        color: #27ae60;
    }
    .badge-primary {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .badge-secondary {
        background-color: #e2e3e5;
        color: #383d41;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 10px 20px;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link:hover {
        color: #1a4d8c;
        background: #f1f5f9;
    }
    .nav-tabs .nav-link.active {
        color: #1a4d8c;
        border-bottom: 3px solid #1a4d8c;
        background: transparent;
        font-weight: 600;
    }
    .alert-warning {
        background-color: #fff3e0;
        border-left: 4px solid #f39c12;
    }
    .alert-info {
        background-color: #e8f4fd;
        border-left: 4px solid #3498db;
    }
    .alert-primary {
        background-color: #e8f4fd;
        border-left: 4px solid #1a4d8c;
    }
    .alert-success {
        background-color: #e6f7ed;
        border-left: 4px solid #27ae60;
    }
    .card {
        border-radius: 16px !important;
        overflow: hidden;
    }
    .card-header {
        background: white !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    .table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
        border-bottom: none;
        background: #f8fafc;
        padding: 10px 12px;
    }
    .table tbody td {
        vertical-align: middle;
        font-size: 13px;
        padding: 10px 12px;
        border-bottom: 1px solid #f1f3f5;
    }
    .table tbody tr:hover {
        background: #f8fafc;
    }
    .table-responsive {
        border-radius: 12px;
    }
    code {
        background: #f1f3f5;
        padding: 2px 10px;
        border-radius: 6px;
        font-size: 12px;
        color: #1a4d8c;
    }
    @media (max-width: 768px) {
        .stat-number {
            font-size: 20px;
        }
        .nav-tabs .nav-link {
            font-size: 12px;
            padding: 8px 12px;
        }
        .table tbody td {
            font-size: 11px;
            padding: 6px 8px;
        }
        .table thead th {
            font-size: 9px;
            padding: 6px 8px;
        }
    }
</style>
@endpush