@extends('layouts.infirmier')

@section('title', 'Bilan initial - ' . $patient->nom_complet)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-notes-medical me-2" style="color: var(--secondary);"></i>
                Bilan initial
            </h1>
            <p class="text-muted mb-0">
                <strong>{{ $patient->nom_complet }}</strong> 
                <span class="text-muted mx-2">|</span>
                <span class="text-muted">{{ $patient->numero_fiche }}</span>
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('infirmier.patients.attente') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="fas fa-stethoscope text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">Formulaire de bilan initial</h5>
                            <small class="text-muted">Veuillez remplir toutes les informations nécessaires</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('infirmier.patients.store-bilan', $patient->id_patient) }}">
                        @csrf
                        <input type="hidden" name="consultation_id" value="{{ $consultation->id_consultation }}">

                        <!-- Informations patient -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info border-0 rounded-4 d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                    <div class="row flex-grow-1">
                                        <div class="col-md-3">
                                            <strong>Patient :</strong> {{ $patient->nom_complet }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Âge :</strong> {{ $patient->age }} ans
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Sexe :</strong> {{ $patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>N° Fiche :</strong> {{ $patient->numero_fiche }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Constantes vitales -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-heartbeat text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary">Constantes vitales</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Poids (kg)</label>
                                    <input type="number" name="poids" class="form-control" 
                                           step="0.1" min="1" max="300" 
                                           value="{{ old('poids') }}" placeholder="75">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Taille (cm)</label>
                                    <input type="number" name="taille" class="form-control" 
                                           step="0.1" min="1" max="300" 
                                           value="{{ old('taille') }}" placeholder="175">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Température (°C)</label>
                                    <input type="number" name="temperature" class="form-control" 
                                           step="0.1" min="30" max="45" 
                                           value="{{ old('temperature') }}" placeholder="36.5">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tension artérielle</label>
                                    <input type="text" name="tension_arterielle" class="form-control" 
                                           value="{{ old('tension_arterielle') }}" placeholder="12/8">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Fréquence cardiaque (bpm)</label>
                                    <input type="number" name="frequence_cardiaque" class="form-control" 
                                           min="20" max="300" value="{{ old('frequence_cardiaque') }}" placeholder="72">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Fréquence respiratoire (/min)</label>
                                    <input type="number" name="frequence_respiratoire" class="form-control" 
                                           min="4" max="60" value="{{ old('frequence_respiratoire') }}" placeholder="16">
                                </div>
                            </div>
                        </div>

                        <!-- Motif et symptômes -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-comment-medical text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary">Motif et symptômes</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Motif de consultation</label>
                                    <textarea name="motif" class="form-control" rows="2" 
                                              placeholder="Raison de la visite du patient">{{ old('motif') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Symptômes</label>
                                    <textarea name="symptomes" class="form-control" rows="2" 
                                              placeholder="Symptômes rapportés par le patient">{{ old('symptomes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary">Observations</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Observations de l'infirmier</label>
                                    <textarea name="observations" class="form-control" rows="2" 
                                              placeholder="Remarques, constatations...">{{ old('observations') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Allergies et traitements -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-pills text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary">Allergies et traitements</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Allergies</label>
                                    <textarea name="allergies" class="form-control" rows="2" 
                                              placeholder="Médicaments, aliments, pollen...">{{ old('allergies') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Traitements en cours</label>
                                    <textarea name="traitements_en_cours" class="form-control" rows="2" 
                                              placeholder="Médicaments, posologie...">{{ old('traitements_en_cours') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('infirmier.patients.attente') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="fas fa-paper-plane me-2"></i> Transmettre au médecin
                            </button>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Après validation, le bilan sera transmis au médecin traitant
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control, .form-select {
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        border-color: #e2e8f0;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2c6e9e;
        box-shadow: 0 0 0 3px rgba(44, 110, 158, 0.1);
    }
</style>
@endpush