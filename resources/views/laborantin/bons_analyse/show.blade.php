@extends('layouts.laborantin')

@section('title', 'Bon d\'analyse N°' . $bon->numero_bon)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-medical me-2" style="color: #1a4d6b;"></i>
                Bon d'analyse <span class="text-muted">N° {{ $bon->numero_bon }}</span>
            </h1>
            <p class="text-muted mb-0">
                {!! $bon->statut_badge !!}
                <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : ($bon->priorite == 'moyen' ? 'warning' : 'primary') }}">
                    {{ $bon->priorite == 'urgent' ? '🚨 Urgent' : ($bon->priorite == 'moyen' ? '⚠️ Moyen' : '✅ Normal') }}
                </span>
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('laborantin.bons-analyse.print', $bon->id_bon) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i> Imprimer
            </a>
            @if($bon->statut == 'termine')
            <form method="POST" action="{{ route('laborantin.bons-analyse.retourner', $bon->id_bon) }}" style="display:inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer le retour du bon au médecin ?')">
                    <i class="fas fa-paper-plane me-2"></i> Retourner au médecin
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Infos Patient -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Informations générales du patient
                </div>
                <div class="card-body">
                    <p><strong>Nom, Post-nom et Prénom :</strong> {{ $bon->patient->nom_complet ?? 'N/A' }}</p>
                    <p><strong>Sexe :</strong> {{ $bon->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                    <p><strong>Date de naissance :</strong> {{ isset($bon->patient->date_naissance) ? Carbon\Carbon::parse($bon->patient->date_naissance)->format('d/m/Y') : 'N/A' }}</p>
                    <p><strong>Téléphone :</strong> {{ $bon->patient->telephone ?? 'N/A' }}</p>
                    @if($bon->hospitalise)
                        <p><strong>Hospitalisé :</strong> Oui - Pavillon {{ $bon->pavillon }}, Chambre {{ $bon->chambre }}, Lit {{ $bon->lit }}</p>
                    @else
                        <p><strong>Hospitalisé :</strong> Non</p>
                        <p><strong>Adresse :</strong> {{ $bon->adresse_domicile ?? $bon->patient->adresse ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>

            <!-- Infos Médecin -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-md me-2"></i> Médecin/Clinicién demandeur
                </div>
                <div class="card-body">
                    <p><strong>Nom et prénom :</strong> Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</p>
                    <p><strong>Date :</strong> {{ Carbon\Carbon::parse($bon->date_demande)->format('d/m/Y') }}</p>
                    <p><strong>Heure :</strong> {{ $bon->heure_demande }}</p>
                    <p><strong>Service :</strong> {{ $bon->service_demandeur }}</p>
                    <p><strong>Téléphone :</strong> {{ $bon->telephone_demandeur }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Infos Infirmier -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-nurse me-2"></i> Infirmier
                </div>
                <div class="card-body">
                    @if($bon->infirmier)
                        <p><strong>Nom :</strong> {{ $bon->infirmier->nom_complet ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted">Non assigné</p>
                    @endif
                </div>
            </div>

            <!-- Préleveur -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-syringe me-2"></i> Préleveur
                </div>
                <div class="card-body">
                    @if($bon->statut == 'en_attente')
                        <p class="text-muted">En attente de prélèvement</p>
                    @else
                        <p><strong>Nom :</strong> {{ $bon->nom_preleveur ?? 'N/A' }}</p>
                        <p><strong>Prénom :</strong> {{ $bon->prenom_preleveur ?? 'N/A' }}</p>
                        <p><strong>Date :</strong> {{ $bon->date_prelevement ? Carbon\Carbon::parse($bon->date_prelevement)->format('d/m/Y') : '-' }}</p>
                        <p><strong>Heure :</strong> {{ $bon->heure_prelevement ?? '-' }}</p>
                        <p><strong>N° Id. Client :</strong> {{ $bon->id_client_labo ?? '-' }}</p>
                        <p><strong>Laborantin :</strong> {{ $bon->laborantin->nom_complet ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>

            <!-- Type d'échantillon -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-vial me-2"></i> Type d'échantillon
                </div>
                <div class="card-body">
                    <p><strong>Type :</strong> 
                        @if($bon->type_echantillon == 'sang') Sang
                        @elseif($bon->type_echantillon == 'selles') Selles
                        @elseif($bon->type_echantillon == 'urines') Urines
                        @elseif($bon->type_echantillon == 'lcr') LCR
                        @elseif($bon->type_echantillon == 'crachat') Crachat
                        @elseif($bon->type_echantillon == 'autres') Autres
                        @else Non spécifié
                        @endif
                    </p>
                    @if($bon->autres_echantillon)
                        <p><strong>Précision :</strong> {{ $bon->autres_echantillon }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ⭐ BILAN INFIRMIER - AJOUTÉ ⭐ -->
    @if($bon->poids_infirmier || $bon->taille_infirmier || $bon->temperature_infirmier)
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <i class="fas fa-user-nurse me-2"></i> Bilan initial - Infirmier
        </div>
        <div class="card-body">
            <div class="row">
                @if($bon->poids_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">Poids</label>
                        <p class="fw-bold">{{ $bon->poids_infirmier }} kg</p>
                    </div>
                </div>
                @endif
                @if($bon->taille_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">Taille</label>
                        <p class="fw-bold">{{ $bon->taille_infirmier }} cm</p>
                    </div>
                </div>
                @endif
                @if($bon->temperature_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">Température</label>
                        <p class="fw-bold">{{ $bon->temperature_infirmier }} °C</p>
                    </div>
                </div>
                @endif
                @if($bon->tension_arterielle_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">Tension</label>
                        <p class="fw-bold">{{ $bon->tension_arterielle_infirmier }}</p>
                    </div>
                </div>
                @endif
                @if($bon->frequence_cardiaque_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">FC</label>
                        <p class="fw-bold">{{ $bon->frequence_cardiaque_infirmier }} bpm</p>
                    </div>
                </div>
                @endif
                @if($bon->frequence_respiratoire_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">FR</label>
                        <p class="fw-bold">{{ $bon->frequence_respiratoire_infirmier }}/min</p>
                    </div>
                </div>
                @endif
                @if($bon->imc_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">IMC</label>
                        <p class="fw-bold">{{ $bon->imc_infirmier }}</p>
                    </div>
                </div>
                @endif
                @if($bon->spo2_infirmier)
                <div class="col-md-3">
                    <div class="info-item">
                        <label class="text-muted small">SpO2</label>
                        <p class="fw-bold">{{ $bon->spo2_infirmier }}%</p>
                    </div>
                </div>
                @endif
            </div>
            @if($bon->motif_infirmier || $bon->symptomes_infirmier || $bon->observations_infirmier)
            <hr>
            <div class="row">
                @if($bon->motif_infirmier)
                <div class="col-md-4">
                    <label class="text-muted small">Motif</label>
                    <p class="fw-bold">{{ $bon->motif_infirmier }}</p>
                </div>
                @endif
                @if($bon->symptomes_infirmier)
                <div class="col-md-4">
                    <label class="text-muted small">Symptômes</label>
                    <p class="fw-bold">{{ $bon->symptomes_infirmier }}</p>
                </div>
                @endif
                @if($bon->observations_infirmier)
                <div class="col-md-4">
                    <label class="text-muted small">Observations</label>
                    <p class="fw-bold">{{ $bon->observations_infirmier }}</p>
                </div>
                @endif
            </div>
            @endif
            @if($bon->allergies_infirmier || $bon->traitements_infirmier)
            <hr>
            <div class="row">
                @if($bon->allergies_infirmier)
                <div class="col-md-6">
                    <label class="text-muted small text-danger">Allergies</label>
                    <p class="fw-bold text-danger">{{ $bon->allergies_infirmier }}</p>
                </div>
                @endif
                @if($bon->traitements_infirmier)
                <div class="col-md-6">
                    <label class="text-muted small">Traitements</label>
                    <p class="fw-bold">{{ $bon->traitements_infirmier }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Examens et Résultats -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-microscope me-2"></i> Examens et Résultats
        </div>
        <div class="card-body">
            @if($bon->statut == 'preleve' || $bon->statut == 'analyse')
                <form method="POST" action="{{ route('laborantin.bons-analyse.resultats', $bon->id_bon) }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Examen</th>
                                    <th>Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $categories = [
                                        'parasitologie' => 'Parasitologie',
                                        'hematologie' => 'Hématologie',
                                        'microbiologie' => 'Microbiologie',
                                        'biochimie' => 'Biochimie',
                                        'immuno_serologie' => 'Immuno-sérologie'
                                    ];
                                @endphp
                                @if($bon->examens)
                                    @foreach($categories as $catKey => $catLabel)
                                        @if(isset($bon->examens[$catKey]))
                                            @foreach($bon->examens[$catKey] as $examenKey => $valeur)
                                                @if($valeur == 1)
                                                    <tr>
                                                        <td>{{ $catLabel }}</td>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $examenKey)) }}</td>
                                                        <td>
                                                            <input type="text" name="resultats[{{ $catKey }}][{{ $examenKey }}]" 
                                                                   class="form-control" 
                                                                   placeholder="Résultat..." 
                                                                   value="{{ $bon->resultats[$catKey][$examenKey] ?? '' }}">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif

                                <!-- Autres examens -->
                                @if(isset($bon->examens['autres']))
                                    @foreach($bon->examens['autres'] as $index => $valeur)
                                        @if(!empty($valeur))
                                            <tr>
                                                <td>Autres</td>
                                                <td>{{ $valeur }}</td>
                                                <td>
                                                    <input type="text" name="resultats[autres][{{ $index }}]" 
                                                           class="form-control" 
                                                           placeholder="Résultat..."
                                                           value="{{ $bon->resultats['autres'][$index] ?? '' }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Observations du laborantin</label>
                            <textarea name="observations_laborantin" class="form-control" rows="3">{{ $bon->observations_laborantin }}</textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i> Enregistrer les résultats
                            </button>
                        </div>
                    </div>
                </form>
            @elseif($bon->statut == 'termine' || $bon->statut == 'retourne')
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Examen</th>
                                <th>Résultat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $categories = [
                                    'parasitologie' => 'Parasitologie',
                                    'hematologie' => 'Hématologie',
                                    'microbiologie' => 'Microbiologie',
                                    'biochimie' => 'Biochimie',
                                    'immuno_serologie' => 'Immuno-sérologie'
                                ];
                            @endphp
                            @if($bon->examens)
                                @foreach($categories as $catKey => $catLabel)
                                    @if(isset($bon->examens[$catKey]))
                                        @foreach($bon->examens[$catKey] as $examenKey => $valeur)
                                            @if($valeur == 1)
                                                <tr>
                                                    <td>{{ $catLabel }}</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $examenKey)) }}</td>
                                                    <td>
                                                        <strong>{{ $bon->resultats[$catKey][$examenKey] ?? 'Non renseigné' }}</strong>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif

                            <!-- Autres examens -->
                            @if(isset($bon->examens['autres']))
                                @foreach($bon->examens['autres'] as $index => $valeur)
                                    @if(!empty($valeur))
                                        <tr>
                                            <td>Autres</td>
                                            <td>{{ $valeur }}</td>
                                            <td>
                                                <strong>{{ $bon->resultats['autres'][$index] ?? 'Non renseigné' }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($bon->observations_laborantin)
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="fw-bold">Observations du laborantin :</label>
                        <p>{{ $bon->observations_laborantin }}</p>
                    </div>
                </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> 
                    En attente du prélèvement. Une fois le prélèvement effectué, vous pourrez saisir les résultats.
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .info-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 5px;
    }
    .info-item label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 14px;
    }
</style>
@endpush
@endsection