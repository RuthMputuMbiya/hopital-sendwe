@extends('layouts.medecin')

@section('title', 'Modifier la consultation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-edit me-2" style="color: #1a4d6b;"></i>
                Modifier la consultation
            </h1>
            <p class="text-muted mb-0">{{ $consultation->numero_consultacion ?? 'N/A' }}</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Modifier les informations
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medecin.consultations.update', $consultation->id_consultation) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-1 text-primary"></i> Patient <span class="text-danger">*</span>
                                </label>
                                <select name="id_patient" id="patient_select" class="form-select select2 @error('id_patient') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id_patient }}" 
                                            {{ old('id_patient', $consultation->id_patient) == $patient->id_patient ? 'selected' : '' }}>
                                            {{ $patient->nom_complet }} - {{ $patient->numero_fiche }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_patient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <!-- ⭐ SEXE - SAISIE DIRECTE AVEC SELECT ⭐ -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Sexe <span class="text-danger">*</span></label>
                                <select name="sexe" id="patient_sexe_select" class="form-select" required>
                                    <option value="">-- Sélectionnez --</option>
                                    <option value="M" {{ old('sexe', $consultation->patient->sexe ?? '') == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe', $consultation->patient->sexe ?? '') == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                            </div>
                            
                            <!-- ⭐ DATE NAISSANCE - SAISIE DIRECTE ⭐ -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date naissance <span class="text-danger">*</span></label>
                                <input type="date" name="date_naissance" id="patient_date_naissance_input" class="form-control" 
                                       value="{{ old('date_naissance', isset($consultation->patient->date_naissance) ? $consultation->patient->date_naissance->format('Y-m-d') : '') }}" required>
                            </div>
                            
                            <!-- ⭐ TÉLÉPHONE - SAISIE DIRECTE ⭐ -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" name="telephone" id="patient_telephone_input" class="form-control" 
                                       value="{{ old('telephone', $consultation->patient->telephone ?? '') }}" placeholder="+243 XX XXX XXXX" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date consultation</label>
                                <input type="date" name="date_consultation" class="form-control @error('date_consultation') is-invalid @enderror" 
                                       value="{{ old('date_consultation', $consultation->date_consultation?->format('Y-m-d') ?? date('Y-m-d')) }}">
                                @error('date_consultation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Maladie</label>
                                <select name="id_maladie" class="form-select select2">
                                    <option value="">Non spécifiée</option>
                                    @foreach($maladies as $maladie)
                                        <option value="{{ $maladie->id_maladie }}" {{ old('id_maladie', $consultation->id_maladie) == $maladie->id_maladie ? 'selected' : '' }}>
                                            {{ $maladie->nom_maladie }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Heure</label>
                                <input type="time" name="heure_consultation" class="form-control" 
                                       value="{{ old('heure_consultation', $consultation->heure_consultation ?? date('H:i')) }}">
                            </div>
                        </div>
                        
                        <!-- Signes vitaux -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <i class="fas fa-heartbeat me-2"></i> Signes vitaux
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Tension artérielle</label>
                                        <input type="text" name="tension_arterielle" class="form-control" 
                                               placeholder="120/80" value="{{ old('tension_arterielle', $consultation->tension_arterielle) }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Poids (kg)</label>
                                        <input type="number" step="0.1" name="poids" class="form-control" 
                                               placeholder="70.5" value="{{ old('poids', $consultation->poids) }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Taille (cm)</label>
                                        <input type="number" step="0.1" name="taille" class="form-control" 
                                               placeholder="170" value="{{ old('taille', $consultation->taille) }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Température (°C)</label>
                                        <input type="number" step="0.1" name="temperature" class="form-control" 
                                               placeholder="37.0" value="{{ old('temperature', $consultation->temperature) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Fréquence cardiaque</label>
                                        <input type="number" name="frequence_cardiaque" class="form-control" 
                                               placeholder="72 bpm" value="{{ old('frequence_cardiaque', $consultation->frequence_cardiaque) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-question-circle me-1 text-primary"></i> Motif de la consultation <span class="text-danger">*</span>
                            </label>
                            <textarea name="motif" rows="3" class="form-control @error('motif') is-invalid @enderror" 
                                      placeholder="Raison de la consultation..." required>{{ old('motif', $consultation->motif) }}</textarea>
                            @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-head-side-medical me-1 text-primary"></i> Symptômes <span class="text-danger">*</span>
                            </label>
                            <textarea name="symptomes" rows="3" class="form-control @error('symptomes') is-invalid @enderror" 
                                      placeholder="Liste des symptômes..." required>{{ old('symptomes', $consultation->symptomes) }}</textarea>
                            @error('symptomes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-stethoscope me-1 text-primary"></i> Diagnostic <span class="text-danger">*</span>
                            </label>
                            <textarea name="diagnostic" rows="4" class="form-control @error('diagnostic') is-invalid @enderror" 
                                      placeholder="Diagnostic médical..." required>{{ old('diagnostic', $consultation->diagnostic) }}</textarea>
                            @error('diagnostic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-prescription-bottle me-1 text-primary"></i> Prescription
                            </label>
                            <textarea name="prescription" rows="4" class="form-control" 
                                      placeholder="Médicaments, posologie, durée...">{{ old('prescription', $consultation->prescription) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment-dots me-1 text-primary"></i> Observations
                            </label>
                            <textarea name="observations_medecin" rows="3" class="form-control" 
                                      placeholder="Notes et observations supplémentaires...">{{ old('observations_medecin', $consultation->observations_medecin) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-flag-checkered me-1 text-primary"></i> Statut
                            </label>
                            <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                <option value="en_cours" {{ old('statut', $consultation->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ old('statut', $consultation->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ old('statut', $consultation->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Les modifications seront enregistrées et consultables dans l'historique du patient.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap5',
            width: '100%'
        });

        // Quand on change de patient, remplir automatiquement
        $('#patient_select').on('change', function() {
            var option = $(this).find('option:selected');
            var sexe = option.data('sexe') || '';
            var dateNaiss = option.data('date_naissance') || '';
            var telephone = option.data('telephone') || '';

            $('#patient_sexe_select').val(sexe);
            $('#patient_date_naissance_input').val(dateNaiss);
            $('#patient_telephone_input').val(telephone);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .select2-container--bootstrap5 .select2-selection {
        min-height: 38px;
        border-radius: 8px;
    }
</style>
@endpush
@endsection