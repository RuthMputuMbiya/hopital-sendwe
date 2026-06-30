@extends('layouts.laborantin')

@section('title', 'Prélèvement - Bon N°' . $bon->numero_bon)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-syringe me-2" style="color: #1a4d6b;"></i>
                Prélèvement - Bon de demande d'analyse
            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary">N° {{ $bon->numero_bon }}</span>
                <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : ($bon->priorite == 'moyen' ? 'warning' : 'primary') }}">
                    {{ $bon->priorite == 'urgent' ? 'Urgent' : ($bon->priorite == 'moyen' ? 'Moyen' : 'Normal') }}
                </span>
                <span class="badge bg-warning">En attente de prélèvement</span>
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12" data-aos="fade-up">
            <div class="card">
                <div class="card-header" style="background: #1a4d6b; color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical me-2"></i> 
                        LABORATOIRE DE L'HÔPITAL GENERAL PROVINCIAL DE REFERENCE JASON SENDWE
                        <span class="badge bg-warning float-end">BON DE DEMANDE D'ANALYSE</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">LS-FLT-001 / PAGE 1 SUR 1</h6>
                            <hr>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 1. INFORMATIONS GÉNÉRALES DU PATIENT        -->
                    <!-- ========================================== -->
                    <div class="section-title">
                        <i class="fas fa-user me-2"></i> Informations générales du patient
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="info-label">Nom, Post-nom et Prénom</label>
                                <p class="info-value"><strong>{{ $bon->patient->nom_complet ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-group">
                                <label class="info-label">Sexe</label>
                                <p class="info-value"><strong>{{ $bon->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-group">
                                <label class="info-label">Date de naissance</label>
                                <p class="info-value"><strong>{{ isset($bon->patient->date_naissance) ? $bon->patient->date_naissance->format('d/m/Y') : 'N/A' }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-group">
                                <label class="info-label">Téléphone</label>
                                <p class="info-value"><strong>{{ $bon->patient->telephone ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Hospitalisé(e)</label>
                                <p class="info-value">
                                    @if($bon->hospitalise)
                                        <strong>Oui</strong> - Pavillon N° {{ $bon->pavillon ?? 'N/A' }}, Chambre N° {{ $bon->chambre ?? 'N/A' }}, Lit N° {{ $bon->lit ?? 'N/A' }}
                                    @else
                                        <strong>Non</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Adresse du domicile</label>
                                <p class="info-value"><strong>{{ $bon->adresse_domicile ?? $bon->patient->adresse ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- ⭐ BILAN INFIRMIER - AJOUTÉ ⭐ -->
                    @if($bon->poids_infirmier || $bon->taille_infirmier || $bon->temperature_infirmier)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-user-nurse me-2"></i> 
                                    Bilan initial - Infirmier
                                    @if($bon->infirmier)
                                    <span class="badge bg-light text-dark ms-2">
                                        {{ $bon->infirmier->nom_complet ?? 'N/A' }}
                                    </span>
                                    @endif
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
                        </div>
                    </div>
                    @endif

                    <!-- ========================================== -->
                    <!-- 2. MÉDECIN/CLINICIEN DEMANDEUR              -->
                    <!-- ========================================== -->
                    <div class="section-title mt-4">
                        <i class="fas fa-user-md me-2"></i> Médecin/Clinicién demandeur
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="info-label">Nom et prénom</label>
                                <p class="info-value"><strong>Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-group">
                                <label class="info-label">Date</label>
                                <p class="info-value"><strong>{{ $bon->date_demande->format('d/m/Y') }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-group">
                                <label class="info-label">Heure</label>
                                <p class="info-value"><strong>{{ $bon->heure_demande }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-group">
                                <label class="info-label">Service</label>
                                <p class="info-value"><strong>{{ $bon->service_demandeur }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-group">
                                <label class="info-label">Téléphone</label>
                                <p class="info-value"><strong>{{ $bon->telephone_demandeur }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 3. PRÉLEVEUR (LABO/AUTRE)                   -->
                    <!-- ========================================== -->
                    <div class="section-title mt-4">
                        <i class="fas fa-syringe me-2"></i> Préleveur (labo/autre)
                    </div>

                    <form method="POST" action="{{ route('laborantin.bons-analyse.prelever', $bon->id_bon) }}" id="preleverForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="nom_preleveur" class="form-control @error('nom_preleveur') is-invalid @enderror" 
                                           value="{{ old('nom_preleveur', Auth::user()->nom) }}" required>
                                    @error('nom_preleveur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="prenom_preleveur" class="form-control @error('prenom_preleveur') is-invalid @enderror" 
                                           value="{{ old('prenom_preleveur', Auth::user()->prenom) }}" required>
                                    @error('prenom_preleveur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Date prélèvement <span class="text-danger">*</span></label>
                                    <input type="date" name="date_prelevement" class="form-control @error('date_prelevement') is-invalid @enderror" 
                                           value="{{ old('date_prelevement', date('Y-m-d')) }}" required>
                                    @error('date_prelevement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Heure prélèvement <span class="text-danger">*</span></label>
                                    <input type="time" name="heure_prelevement" class="form-control @error('heure_prelevement') is-invalid @enderror" 
                                           value="{{ old('heure_prelevement', date('H:i')) }}" required>
                                    @error('heure_prelevement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">N° Id. Client/labo</label>
                                    <input type="text" name="id_client_labo" class="form-control" value="{{ old('id_client_labo') }}" placeholder="N° client">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Téléphone préleveur</label>
                                    <input type="text" name="telephone_preleveur" class="form-control" value="{{ old('telephone_preleveur', Auth::user()->telephone) }}" placeholder="Téléphone">
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 4. TYPE D'ÉCHANTILLON - SELECTION LIBRE   -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-vial me-2"></i> Types d'échantillons primaires
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-4">
                                    @php
                                        $types = ['sang' => 'Sang', 'selles' => 'Selles', 'urines' => 'Urines', 'lcr' => 'LCR', 'crachat' => 'Crachat', 'autres' => 'Autres'];
                                    @endphp
                                    @foreach($types as $key => $label)
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="{{ $key }}" value="{{ $key }}" 
                                               class="form-check-input" {{ old('type_echantillon', $bon->type_echantillon) == $key ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Précisez si autres...</label>
                                    <input type="text" name="autres_echantillon" class="form-control" 
                                           value="{{ old('autres_echantillon', $bon->autres_echantillon) }}" placeholder="Précisez...">
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 5. EXAMENS DEMANDÉS                        -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-microscope me-2"></i> Examens demandés
                            <span class="text-muted small">(Cochez les examens que vous avez réalisés)</span>
                        </div>

                        <div class="row">
                            @php
                                $hasItems = false;
                                $hasAutres = false;
                                
                                $categories = [
                                    'parasitologie' => ['label' => '🦠 Parasitologie', 'items' => $bon->examens['parasitologie'] ?? []],
                                    'hematologie' => ['label' => '🩸 Hématologie', 'items' => $bon->examens['hematologie'] ?? []],
                                    'microbiologie' => ['label' => '🧫 Microbiologie', 'items' => $bon->examens['microbiologie'] ?? []],
                                    'biochimie' => ['label' => '🧪 Biochimie', 'items' => $bon->examens['biochimie'] ?? []],
                                    'immuno_serologie' => ['label' => '🧬 Immuno-sérologie', 'items' => $bon->examens['immuno_serologie'] ?? []]
                                ];

                                foreach($categories as $cat) {
                                    foreach($cat['items'] as $value) {
                                        if($value == 1) {
                                            $hasItems = true;
                                            break 2;
                                        }
                                    }
                                }

                                if(isset($bon->examens['autres'])) {
                                    foreach($bon->examens['autres'] as $value) {
                                        if(!empty($value)) {
                                            $hasAutres = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            @foreach($categories as $catKey => $cat)
                                @php
                                    $hasCatItems = false;
                                    foreach($cat['items'] as $value) {
                                        if($value == 1) {
                                            $hasCatItems = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @if($hasCatItems)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">{{ $cat['label'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($cat['items'] as $examenKey => $valeur)
                                                @if($valeur == 1)
                                                <div class="form-check">
                                                    <input type="checkbox" name="examens_realises[{{ $catKey }}][{{ $examenKey }}]" 
                                                           value="1" id="{{ $catKey }}_{{ $examenKey }}" class="form-check-input">
                                                    <label class="form-check-label" for="{{ $catKey }}_{{ $examenKey }}">
                                                        {{ ucfirst(str_replace('_', ' ', $examenKey)) }}
                                                    </label>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach

                            <!-- Autres examens -->
                            @if($hasAutres)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">📋 Autres analyses</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($bon->examens['autres'] as $index => $valeur)
                                            @if(!empty($valeur))
                                            <div class="form-check">
                                                <input type="checkbox" name="examens_realises[autres][{{ $index }}]" 
                                                       value="1" id="autres_{{ $index }}" class="form-check-input">
                                                <label class="form-check-label" for="autres_{{ $index }}">
                                                    {{ $valeur }}
                                                </label>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(!$hasItems && !$hasAutres)
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucun examen demandé pour ce bon.
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- ========================================== -->
                        <!-- 6. OBSERVATIONS LABORANTIN                -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-comment-dots me-2"></i> Observations du laborantin
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea name="observations_laborantin" class="form-control" rows="3" 
                                              placeholder="Observations du laborantin...">{{ old('observations_laborantin') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 7. ACTIONS                                -->
                        <!-- ========================================== -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Information :</strong> Une fois le prélèvement enregistré, le bon passera en statut "Prélevé" et vous pourrez saisir les résultats.
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('laborantin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i> Enregistrer le prélèvement
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #1a4d6b;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
        margin-bottom: 15px;
        margin-top: 5px;
    }
    .info-group {
        margin-bottom: 5px;
    }
    .info-label {
        font-size: 11px;
        color: #6c757d;
        font-weight: 500;
        display: block;
        margin-bottom: 0;
    }
    .info-value {
        font-size: 14px;
        margin-bottom: 0;
    }
    .form-check {
        margin-bottom: 3px;
    }
    .form-check-input {
        cursor: pointer;
    }
    .form-check-label {
        cursor: pointer;
        font-size: 13px;
    }
    .card-header h6 {
        margin-bottom: 0;
        color: #1a4d6b;
    }
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