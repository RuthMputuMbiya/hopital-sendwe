@extends('layouts.infirmier')

@section('title', 'Détails du bilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-medical me-2" style="color: var(--secondary);"></i>
                Détails du bilan
            </h1>
            <p class="text-muted mb-0">
                Patient : <strong>{{ $bilan->consultation->patient->nom_complet }}</strong>
                ({{ $bilan->consultation->patient->numero_fiche }})
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('infirmier.bilans.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('infirmier.bilans.print', $bilan->id_bilan) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i> Imprimer
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Informations patient
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="40%">Nom complet</th><td>{{ $bilan->consultation->patient->nom_complet }}</td></tr>
                        <tr><th>N° Fiche</th><td><code>{{ $bilan->consultation->patient->numero_fiche }}</code></td></tr>
                        <tr><th>Sexe</th><td>{{ $bilan->consultation->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</td></tr>
                        <tr><th>Âge</th><td>{{ $bilan->consultation->patient->age }} ans</td></tr>
                        <tr><th>Téléphone</th><td>{{ $bilan->consultation->patient->telephone }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations bilan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="40%">Date du bilan</th><td>{{ $bilan->date_formatee }}</td></tr>
                        <tr><th>Infirmier</th><td>{{ $bilan->infirmier->nom_complet }}</td></tr>
                        <tr><th>Statut</th><td>
                            @if($bilan->est_transmis)
                                <span class="badge bg-success">✅ Transmis au médecin</span>
                            @else
                                <span class="badge bg-warning">⏳ En attente</span>
                            @endif
                        </td></tr>
                        @if($bilan->date_transmission)
                        <tr><th>Date transmission</th><td>{{ $bilan->date_transmission->format('d/m/Y H:i') }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Constantes vitales -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-heartbeat me-2"></i> Constantes vitales
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-weight-scale text-primary fa-2x d-block mb-2"></i>
                        <strong>Poids</strong><br>
                        {{ $bilan->poids ?? '-' }} kg
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-ruler-vertical text-primary fa-2x d-block mb-2"></i>
                        <strong>Taille</strong><br>
                        {{ $bilan->taille ?? '-' }} cm
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-calculator text-{{ $bilan->imc_color }} fa-2x d-block mb-2"></i>
                        <strong>IMC</strong><br>
                        <span class="fw-bold text-{{ $bilan->imc_color }}">{{ $bilan->imc ?? '-' }}</span>
                        <br><small>{{ $bilan->imc_status }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-thermometer-half text-warning fa-2x d-block mb-2"></i>
                        <strong>Température</strong><br>
                        {{ $bilan->temperature ?? '-' }} °C
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-heart text-danger fa-2x d-block mb-2"></i>
                        <strong>Tension artérielle</strong><br>
                        {{ $bilan->tension_arterielle ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-heartbeat text-danger fa-2x d-block mb-2"></i>
                        <strong>Fréquence cardiaque</strong><br>
                        {{ $bilan->frequence_cardiaque ?? '-' }} bpm
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <i class="fas fa-lungs text-info fa-2x d-block mb-2"></i>
                        <strong>Fréquence respiratoire</strong><br>
                        {{ $bilan->frequence_respiratoire ?? '-' }} /min
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations cliniques -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-comment-medical me-2"></i> Motif et symptômes
                </div>
                <div class="card-body">
                    <p><strong>Motif :</strong> {{ $bilan->motif ?? 'Non renseigné' }}</p>
                    <p><strong>Symptômes :</strong> {{ $bilan->symptomes ?? 'Non renseigné' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-eye me-2"></i> Observations et allergies
                </div>
                <div class="card-body">
                    <p><strong>Observations :</strong> {{ $bilan->observations ?? 'Aucune' }}</p>
                    <p><strong>Allergies :</strong> {{ $bilan->allergies ?? 'Aucune' }}</p>
                    <p><strong>Traitements :</strong> {{ $bilan->traitements_en_cours ?? 'Aucun' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection