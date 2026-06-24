@extends('layouts.laborantin')

@section('title', 'Détails examen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div><h1 class="h2"><i class="fas fa-file-alt me-2" style="color: var(--secondary);"></i>Détails examen</h1><p class="text-muted">{{ $examen->numero_examen }}</p></div>
        <div>
            <a href="{{ route('laborantin.examens.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            @if($examen->statut == 'en_attente')
                <a href="{{ route('laborantin.examens.resultat', $examen->id_examen) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Saisir résultat</a>
            @endif
            <a href="#" class="btn btn-success" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimer</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>Informations générales</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="40%">N° Examen</th><td><code>{{ $examen->numero_examen }}</code></td></tr>
                        <tr><th>Type d'examen</th><td><strong>{{ $examen->type_examen }}</strong></td></tr>
                        <tr><th>Date de l'examen</th><td>{{ $examen->date_examen->format('d/m/Y') }} à {{ $examen->heure_examen }}</td></tr>
                        <tr><th>Statut</th><td>@if($examen->statut == 'en_attente')<span class="badge badge-warning">En attente</span>@elseif($examen->statut == 'termine')<span class="badge badge-success">Terminé</span>@else<span class="badge badge-danger">Annulé</span>@endif</td></tr>
                        <tr><th>Priorité</th><td><span class="badge {{ $examen->priorite == 'urgent' ? 'badge-danger' : 'badge-info' }}">{{ ucfirst($examen->priorite ?? 'normal') }}</span></td></tr>
                        <tr><th>Date création</th><td>{{ $examen->created_at->format('d/m/Y H:i') }}</td></tr>
                        <tr><th>Laborantin</th><td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-user me-2"></i>Patient</div>
                <div class="card-body">
                    <h5>{{ $examen->patient->nom_complet ?? 'N/A' }}</h5>
                    <p class="text-muted mb-2">N° Fiche: {{ $examen->patient->numero_fiche ?? 'N/A' }}</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $examen->patient->telephone ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-location-dot me-2"></i>{{ $examen->patient->adresse ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Résultat de l'examen</div>
                <div class="card-body">
                    @if($examen->resultat)
                        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Résultat disponible</div>
                        <div class="p-3 bg-light rounded">{!! nl2br(e($examen->resultat)) !!}</div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Résultat non encore disponible</p>
                            <a href="{{ route('laborantin.examens.resultat', $examen->id_examen) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Saisir le résultat</a>
                        </div>
                    @endif
                </div>
            </div>

            @if($examen->commentaires)
            <div class="card mt-4">
                <div class="card-header"><i class="fas fa-comment me-2"></i>Commentaires</div>
                <div class="card-body">{{ $examen->commentaires }}</div>
            </div>
            @endif

            @if($examen->id_maladie)
            <div class="card mt-4">
                <div class="card-header"><i class="fas fa-disease me-2"></i>Maladie associée</div>
                <div class="card-body"><strong>{{ $examen->maladie->nom_maladie }}</strong><p class="mt-2">{{ $examen->maladie->description }}</p></div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection