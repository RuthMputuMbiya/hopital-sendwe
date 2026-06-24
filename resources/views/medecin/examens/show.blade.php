@extends('layouts.medecin')

@section('title', 'Détails examen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-flask me-2" style="color: #1a4d6b;"></i>
                Détails de l'examen
            </h1>
            <p class="text-muted mb-0">{{ $examen->numero_examen ?? 'N/A' }} - {{ $examen->type_examen ?? 'N/A' }}</p>
        </div>
        <div data-aos="fade-left">
            <div class="btn-group">
                <a href="{{ route('medecin.examens.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
                @if($examen->statut == 'termine')
                    <a href="{{ route('medecin.examens.print', $examen->id_examen) }}" class="btn btn-success">
                        <i class="fas fa-print me-2"></i> Imprimer résultat
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations examen -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations générales
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="40%">N° Examen</th><td><code>{{ $examen->numero_examen ?? 'N/A' }}</code></td></tr>
                        <tr><th>Type d'examen</th><td><strong>{{ $examen->type_examen ?? 'N/A' }}</strong></td></tr>
                        <tr><th>Date de l'examen</th><td>{{ isset($examen->date_examen) ? $examen->date_examen->format('d/m/Y') : 'N/A' }} à {{ $examen->heure_examen ?? 'N/A' }}</td></tr>
                        <tr><th>Laborantin</th><td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</td></tr>
                        <tr><th>Statut</th><td>
                            @if($examen->statut == 'termine')
                                <span class="badge bg-success">Terminé</span>
                            @elseif($examen->statut == 'en_attente')
                                <span class="badge bg-warning text-dark">En attente</span>
                            @else
                                <span class="badge bg-danger">Annulé</span>
                            @endif
                        </td></tr>
                    </table>
                </div>
            </div>

            <!-- Informations patient -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Patient
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $examen->patient->nom_complet ?? 'N/A' }}</h5>
                    <p class="text-muted mb-2">{{ $examen->patient->numero_fiche ?? 'N/A' }}</p>
                    <hr>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i> {{ $examen->patient->telephone ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-calendar me-2 text-primary"></i> Né(e) le: {{ isset($examen->patient->date_naissance) ? \Carbon\Carbon::parse($examen->patient->date_naissance)->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Résultat de l'examen -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Résultat de l'examen
                    @if($examen->statut == 'termine')
                        <span class="badge bg-success ms-2">Disponible</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($examen->statut == 'termine')
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i> Résultat disponible
                            <div class="mt-2">
                                <a href="{{ route('medecin.examens.print', $examen->id_examen) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-print me-1"></i> Imprimer le résultat
                                </a>
                            </div>
                        </div>
                        <div class="resultat-content p-3 bg-light rounded">
                            {!! nl2br(e($examen->resultat)) !!}
                        </div>
                    @elseif($examen->statut == 'en_attente')
                        <div class="text-center py-5">
                            <i class="fas fa-hourglass-half fa-4x text-warning mb-3 d-block"></i>
                            <h5 class="text-warning">Examen en cours</h5>
                            <p class="text-muted">Le résultat n'est pas encore disponible. Veuillez revenir plus tard.</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ban fa-4x text-danger mb-3 d-block"></i>
                            <h5 class="text-danger">Examen annulé</h5>
                            <p class="text-muted">Cet examen a été annulé.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Autres examens du patient -->
            @if(isset($autresExamens) && $autresExamens->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i> Autres examens du patient
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autresExamens as $autre)
                                <tr>
                                    <td>{{ isset($autre->date_examen) ? $autre->date_examen->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $autre->type_examen ?? 'N/A' }}</td>
                                    <td>
                                        @if($autre->statut == 'termine')
                                            <span class="badge bg-success">Terminé</span>
                                        @elseif($autre->statut == 'en_attente')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @else
                                            <span class="badge bg-danger">Annulé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('medecin.examens.show', $autre->id_examen) }}" class="btn btn-sm btn-outline-primary">
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

@push('styles')
<style>
    .resultat-content {
        font-family: 'Inter', monospace;
        font-size: 14px;
        line-height: 1.6;
        white-space: pre-wrap;
    }
</style>
@endpush
@endsection