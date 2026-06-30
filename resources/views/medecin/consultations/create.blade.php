@extends('layouts.medecin')

@section('title', 'Nouvelle consultation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-plus-circle me-2" style="color: #1a4d6b;"></i>
                Nouvelle consultation
            </h1>
            <p class="text-muted mb-0">Enregistrer une consultation médicale avec bon d'analyse</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.consultations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-medical me-2"></i> Consultation et Bon de demande d'analyse
                    <span class="badge bg-info ms-2">LS-FLT-001</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medecin.consultations.store') }}" id="consultationForm">
                        @csrf

                        <!-- ========================================== -->
                        <!-- 1. INFORMATIONS GÉNÉRALES DU PATIENT        -->
                        <!-- ========================================== -->
                        <div class="section-title">
                            <i class="fas fa-user me-2"></i> Informations générales du patient
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Patient <span class="text-danger">*</span></label>
                                <select name="id_patient" id="patient_select" class="form-select select2 @error('id_patient') is-invalid @enderror" required>
                                    <option value="">-- Sélectionnez un patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id_patient }}" 
                                            data-nom="{{ $patient->nom }}"
                                            data-postnom="{{ $patient->postnom }}"
                                            data-prenom="{{ $patient->prenom }}"
                                            data-sexe="{{ $patient->sexe }}"
                                            data-date_naissance="{{ $patient->date_naissance }}"
                                            data-telephone="{{ $patient->telephone }}"
                                            data-adresse="{{ $patient->adresse }}"
                                            {{ old('id_patient', $selectedPatient->id_patient ?? '') == $patient->id_patient ? 'selected' : '' }}>
                                            {{ $patient->nom_complet }} - {{ $patient->numero_fiche }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_patient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- ⭐ BILAN INFIRMIER - AFFICHAGE AUTOMATIQUE ⭐ -->
                        @if(isset($bilanInfirmier) && $bilanInfirmier)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-user-nurse me-2"></i> 
                                                Bilan initial effectué par l'infirmier
                                            </div>
                                            <div>
                                                <span class="badge bg-light text-dark me-2">
                                                    <i class="fas fa-user me-1"></i> 
                                                    @if(isset($infirmier) && $infirmier)
                                                        {{ $infirmier->nom_complet }}
                                                    @elseif($bilanInfirmier->infirmier)
                                                        {{ $bilanInfirmier->infirmier->nom_complet }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-clock me-1"></i> {{ $bilanInfirmier->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Poids</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->poids ?? 'N/A' }} kg</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Taille</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->taille ?? 'N/A' }} cm</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Température</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->temperature ?? 'N/A' }} °C</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Tension artérielle</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->tension_arterielle ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Fréquence cardiaque</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->frequence_cardiaque ?? 'N/A' }} bpm</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">Fréquence respiratoire</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->frequence_respiratoire ?? 'N/A' }}/min</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item">
                                                    <label class="text-muted small">SpO2</label>
                                                    <p class="fw-bold">{{ $bilanInfirmier->spo2 ?? 'N/A' }} %</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($bilanInfirmier->motif || $bilanInfirmier->symptomes || $bilanInfirmier->observations)
                                        <hr>
                                        <div class="row">
                                            @if($bilanInfirmier->motif)
                                            <div class="col-md-4">
                                                <label class="text-muted small">Motif</label>
                                                <p class="fw-bold">{{ $bilanInfirmier->motif }}</p>
                                            </div>
                                            @endif
                                            @if($bilanInfirmier->symptomes)
                                            <div class="col-md-4">
                                                <label class="text-muted small">Symptômes</label>
                                                <p class="fw-bold">{{ $bilanInfirmier->symptomes }}</p>
                                            </div>
                                            @endif
                                            @if($bilanInfirmier->observations)
                                            <div class="col-md-4">
                                                <label class="text-muted small">Observations</label>
                                                <p class="fw-bold">{{ $bilanInfirmier->observations }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        @if($bilanInfirmier->allergies || $bilanInfirmier->traitements_en_cours)
                                        <hr>
                                        <div class="row">
                                            @if($bilanInfirmier->allergies)
                                            <div class="col-md-6">
                                                <label class="text-muted small text-danger">⚠️ Allergies</label>
                                                <p class="fw-bold text-danger">{{ $bilanInfirmier->allergies }}</p>
                                            </div>
                                            @endif
                                            @if($bilanInfirmier->traitements_en_cours)
                                            <div class="col-md-6">
                                                <label class="text-muted small">Traitements en cours</label>
                                                <p class="fw-bold">{{ $bilanInfirmier->traitements_en_cours }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif(isset($selectedPatient) && $selectedPatient)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Aucun bilan infirmier trouvé</strong> pour ce patient.
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- ========================================== -->
                        <!-- 2. MÉDECIN/CLINICIEN DEMANDEUR               -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-user-md me-2"></i> Médecin/Clinicién demandeur
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nom et prénom</label>
                                <input type="text" class="form-control" value="Dr. {{ Auth::user()->nom_complet }}" readonly>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date_demande" class="form-control" value="{{ old('date_demande', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Heure</label>
                                <input type="time" name="heure_demande" class="form-control" value="{{ old('heure_demande', date('H:i')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Service</label>
                                <input type="text" name="service_demandeur" class="form-control" placeholder="Médecine Générale" value="{{ old('service_demandeur', Auth::user()->specialite ?? 'Médecine Générale') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone_demandeur" class="form-control" placeholder="Téléphone" value="{{ old('telephone_demandeur', Auth::user()->telephone ?? '') }}">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 3. PRÉLEVEUR (LABO/AUTRE)                   -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-syringe me-2"></i> Préleveur (labo/autre)
                            <small class="text-muted">(Rempli par le laborantin)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" placeholder="À remplir par le labo" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" placeholder="À remplir par le labo" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date prélèvement</label>
                                <input type="text" class="form-control" placeholder="--/--/----" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Heure</label>
                                <input type="text" class="form-control" placeholder="--:--" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">N° Id. Client/labo</label>
                                <input type="text" class="form-control" placeholder="À remplir" readonly style="background:#f8f9fa;">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 4. TYPE D'ÉCHANTILLON                       -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-vial me-2"></i> Types d'échantillons primaires
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="sang" value="sang" class="form-check-input" {{ old('type_echantillon') == 'sang' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sang">Sang</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="selles" value="selles" class="form-check-input" {{ old('type_echantillon') == 'selles' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="selles">Selles</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="urines" value="urines" class="form-check-input" {{ old('type_echantillon') == 'urines' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="urines">Urines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="lcr" value="lcr" class="form-check-input" {{ old('type_echantillon') == 'lcr' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lcr">LCR</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="crachat" value="crachat" class="form-check-input" {{ old('type_echantillon') == 'crachat' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="crachat">Crachat</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="autres" value="autres" class="form-check-input" {{ old('type_echantillon') == 'autres' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="autres">Autres</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <input type="text" name="autres_echantillon" class="form-control" placeholder="Précisez si autres..." value="{{ old('autres_echantillon') }}">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 5. EXAMENS PAR CATÉGORIE                   -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-microscope me-2"></i> Examens demandés
                        </div>

                        <div class="row">
                            @foreach($examens as $categorie => $items)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            @if($categorie == 'parasitologie') 🦠
                                            @elseif($categorie == 'hematologie') 🩸
                                            @elseif($categorie == 'microbiologie') 🧫
                                            @elseif($categorie == 'biochimie') 🧪
                                            @elseif($categorie == 'immuno_serologie') 🧬
                                            @endif
                                            {{ ucfirst(str_replace('_', ' ', $categorie)) }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($items as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[{{ $categorie }}][{{ $key }}]" 
                                                   value="1" id="{{ $categorie }}_{{ $key }}" class="form-check-input" {{ old('examens.'.$categorie.'.'.$key) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $categorie }}_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- ========================================== -->
                        <!-- 6. CONSULTATION - CHAMPS MÉDECIN            -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-stethoscope me-2"></i> Détails de la consultation (Médecin)
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Motif de la consultation <span class="text-danger">*</span></label>
                                <textarea name="motif" rows="2" class="form-control @error('motif') is-invalid @enderror" 
                                          placeholder="Raison de la consultation..." required
                                          @if(isset($bilanInfirmier) && $bilanInfirmier && $bilanInfirmier->motif) readonly style="background:#f8f9fa;" @endif>{{ old('motif', $bilanInfirmier->motif ?? '') }}</textarea>
                                @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Symptômes <span class="text-danger">*</span></label>
                                <textarea name="symptomes" rows="3" class="form-control @error('symptomes') is-invalid @enderror" 
                                          placeholder="Liste des symptômes..." required
                                          @if(isset($bilanInfirmier) && $bilanInfirmier && $bilanInfirmier->symptomes) readonly style="background:#f8f9fa;" @endif>{{ old('symptomes', $bilanInfirmier->symptomes ?? '') }}</textarea>
                                @error('symptomes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Diagnostic <span class="text-danger">*</span></label>
                                <textarea name="diagnostic" rows="3" class="form-control @error('diagnostic') is-invalid @enderror" 
                                          placeholder="Diagnostic médical..." required>{{ old('diagnostic') }}</textarea>
                                @error('diagnostic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prescription</label>
                                <textarea name="prescription" rows="3" class="form-control" 
                                          placeholder="Médicaments, posologie, durée...">{{ old('prescription') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observations du médecin</label>
                                <textarea name="observations_medecin" rows="3" class="form-control" 
                                          placeholder="Notes et observations supplémentaires...">{{ old('observations_medecin') }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date consultation</label>
                                <input type="date" name="date_consultation" class="form-control @error('date_consultation') is-invalid @enderror" 
                                       value="{{ old('date_consultation', date('Y-m-d')) }}">
                                @error('date_consultation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Maladie</label>
                                <select name="id_maladie" class="form-select select2">
                                    <option value="">Non spécifiée</option>
                                    @foreach($maladies as $maladie)
                                        <option value="{{ $maladie->id_maladie }}" {{ old('id_maladie') == $maladie->id_maladie ? 'selected' : '' }}>
                                            {{ $maladie->nom_maladie }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Moyen</label>
                                <select name="moyen" class="form-select">
                                    <option value="">-- Sélectionnez --</option>
                                    <option value="Consultation" {{ old('moyen') == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                                    <option value="Urgence" {{ old('moyen') == 'Urgence' ? 'selected' : '' }}>Urgence</option>
                                    <option value="Référence" {{ old('moyen') == 'Référence' ? 'selected' : '' }}>Référence</option>
                                    <option value="Suivi" {{ old('moyen') == 'Suivi' ? 'selected' : '' }}>Suivi</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Priorité</label>
                                <select name="priorite" class="form-select">
                                    <option value="normal" {{ old('priorite', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="moyen" {{ old('priorite') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                    <option value="urgent" {{ old('priorite') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 7. HOSPITALISATION                         -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-hospital me-2"></i> Hospitalisation
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Hospitalisé(e)</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="hospitalise" id="hospitalise" class="form-check-input" value="1" {{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hospitalise">Oui</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="pavillonGroup" style="{{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Pavillon <span class="text-danger">*</span></label>
                                <input type="text" name="pavillon" class="form-control" placeholder="N° Pavillon" value="{{ old('pavillon', $bilanInfirmier->pavillon ?? '') }}" {{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'required' : '' }}>
                            </div>
                            <div class="col-md-3" id="chambreGroup" style="{{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Chambre <span class="text-danger">*</span></label>
                                <input type="text" name="chambre" class="form-control" placeholder="N° Chambre" value="{{ old('chambre', $bilanInfirmier->chambre ?? '') }}" {{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'required' : '' }}>
                            </div>
                            <div class="col-md-3" id="litGroup" style="{{ old('hospitalise', $bilanInfirmier->hospitalise ?? false) ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Lit</label>
                                <input type="text" name="lit" class="form-control" placeholder="N° Lit" value="{{ old('lit', $bilanInfirmier->lit ?? '') }}">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Adresse du domicile</label>
                                <input type="text" name="adresse_domicile" id="patient_adresse" class="form-control" placeholder="Adresse du patient" value="{{ old('adresse_domicile', $selectedPatient->adresse ?? '') }}">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 8. ACTIONS                                -->
                        <!-- ========================================== -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Un numéro de consultation et un bon d'analyse seront générés automatiquement.
                                    @if(isset($bilanInfirmier) && $bilanInfirmier)
                                    <br><i class="fas fa-check-circle text-success me-1"></i> 
                                    <strong>Bilan infirmier intégré automatiquement.</strong>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i> Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Enregistrer la consultation
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap5',
            width: '100%'
        });

        $('#hospitalise').on('change', function() {
            if ($(this).is(':checked')) {
                $('#pavillonGroup').show(300);
                $('#chambreGroup').show(300);
                $('#litGroup').show(300);
                $('#pavillonGroup input, #chambreGroup input').prop('required', true);
            } else {
                $('#pavillonGroup').hide(300);
                $('#chambreGroup').hide(300);
                $('#litGroup').hide(300);
                $('#pavillonGroup input, #chambreGroup input').prop('required', false);
            }
        });

        if ($('#hospitalise').is(':checked')) {
            $('#pavillonGroup').show();
            $('#chambreGroup').show();
            $('#litGroup').show();
            $('#pavillonGroup input, #chambreGroup input').prop('required', true);
        }

        $('#patient_select').on('change', function() {
            var patientId = $(this).val();
            if (patientId) {
                window.location.href = "{{ route('medecin.consultations.create') }}?patientId=" + patientId;
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a4d6b;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
        margin-bottom: 20px;
    }
    .select2-container--bootstrap5 .select2-selection {
        min-height: 38px;
        border-radius: 8px;
    }
    .form-check-input {
        cursor: pointer;
    }
    #pavillonGroup, #chambreGroup, #litGroup {
        transition: all 0.3s ease;
        display: none;
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
    textarea[readonly], input[readonly] {
        background: #f8f9fa !important;
        cursor: default;
    }
    .card.border-success {
        border-color: #28a745 !important;
    }
</style>
@endpush
@endsection