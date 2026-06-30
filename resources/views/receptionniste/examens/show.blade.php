@extends('layouts.receptionniste')

@section('title', 'Détail résultat examen')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- En-tête -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div data-aos="fade-right">
                    <h1 class="h2 mb-1">
                        <i class="fas fa-file-alt me-2" style="color: var(--secondary);"></i>
                        Résultat d'examen
                    </h1>
                    <p class="text-muted mb-0">
                        <strong>{{ $examen->numero_examen ?? 'N/A' }}</strong> - 
                        {{ $examen->type_examen ?? 'N/A' }}
                    </p>
                </div>
                <div data-aos="fade-left" class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('receptionniste.examens.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                    <a href="{{ route('receptionniste.examens.print', $examen->id_examen) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-2"></i> Imprimer
                    </a>
                </div>
            </div>

            <!-- Informations -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="fas fa-microscope text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">Résultat d'examen</h5>
                            <small class="text-muted">Informations détaillées du résultat</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Colonne gauche -->
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <h6 class="text-primary fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Informations examen</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>N° Examen</strong></td>
                                        <td><code>{{ $examen->numero_examen ?? 'N/A' }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date</strong></td>
                                        <td>{{ isset($examen->date_examen) ? \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Heure</strong></td>
                                        <td>{{ $examen->heure_examen ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type d'examen</strong></td>
                                        <td><span class="badge bg-primary">{{ $examen->type_examen ?? 'N/A' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Statut</strong></td>
                                        <td>
                                            @if($examen->statut == 'termine')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Terminé</span>
                                            @elseif($examen->statut == 'en_attente')
                                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> En attente</span>
                                            @elseif($examen->statut == 'en_cours')
                                                <span class="badge bg-info"><i class="fas fa-spinner me-1"></i> En cours</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $examen->statut ?? 'Inconnu' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Laborantin</strong></td>
                                        <td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Colonne droite -->
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user me-2"></i>Informations patient</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Patient</strong></td>
                                        <td><strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>N° Fiche</strong></td>
                                        <td><code>{{ $examen->patient->numero_fiche ?? 'N/A' }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sexe</strong></td>
                                        <td>{{ $examen->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Âge</strong></td>
                                        <td>{{ $examen->patient->age ?? 'N/A' }} ans</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Téléphone</strong></td>
                                        <td>{{ $examen->patient->telephone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Médecin traitant</strong></td>
                                        <td>{{ $examen->consultation->medecin->nom_complet ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Résultat -->
                    <div class="mt-4">
                        <div class="border rounded-3 p-3">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fas fa-chart-line me-2"></i>Résultat de l'examen
                                @if($examen->statut == 'termine')
                                    <span class="badge bg-success ms-2">Disponible</span>
                                @endif
                            </h6>
                            @if($examen->statut == 'termine' && $examen->resultat)
                                <div class="resultat-content p-3 bg-white rounded-3 border">
                                    {!! nl2br(e($examen->resultat)) !!}
                                </div>
                                @if($examen->observations)
                                    <div class="mt-3">
                                        <strong class="text-muted"><i class="fas fa-comment me-1"></i> Observations :</strong>
                                        <p class="mb-0 mt-1">{{ $examen->observations }}</p>
                                    </div>
                                @endif
                                <div class="mt-3">
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar-check me-1"></i> 
                                        Résultat disponible depuis : {{ $examen->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @elseif($examen->statut == 'en_attente')
                                <div class="text-center py-4">
                                    <i class="fas fa-hourglass-half fa-3x text-warning mb-3 d-block"></i>
                                    <h5 class="text-warning">Examen en attente</h5>
                                    <p class="text-muted">Le résultat n'est pas encore disponible. Veuillez revenir plus tard.</p>
                                </div>
                            @elseif($examen->statut == 'en_cours')
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-3x text-info mb-3 d-block"></i>
                                    <h5 class="text-info">Examen en cours</h5>
                                    <p class="text-muted">L'analyse est en cours de réalisation par le laborantin.</p>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-ban fa-3x text-danger mb-3 d-block"></i>
                                    <h5 class="text-danger">Examen non disponible</h5>
                                    <p class="text-muted">Ce résultat n'est pas encore disponible.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bouton d'action -->
                    @if($examen->statut == 'termine' && $examen->resultat)
                    <div class="mt-4 text-end">
                        <a href="{{ route('receptionniste.examens.print', $examen->id_examen) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> Télécharger PDF
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .resultat-content {
        font-family: 'Inter', monospace;
        font-size: 14px;
        line-height: 1.8;
        white-space: pre-wrap;
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    .table-borderless td, .table-borderless th {
        padding: 6px 0;
        font-size: 14px;
    }
    .table-borderless td:first-child {
        color: #6c757d;
    }
    .border {
        border-color: #e9ecef !important;
    }
    .badge {
        font-weight: 500;
        font-size: 12px;
        padding: 5px 12px;
    }
    .card {
        border-radius: 16px !important;
    }
    .card-header {
        border-bottom: 1px solid #e9ecef !important;
    }
    @media (max-width: 768px) {
        .table-borderless td, .table-borderless th {
            font-size: 12px;
        }
        .resultat-content {
            font-size: 12px;
            padding: 12px;
        }
    }
</style>
@endpush