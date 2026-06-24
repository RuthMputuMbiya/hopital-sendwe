@extends('layouts.medecin')

@section('title', 'Nouveau bon d\'analyse')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 mb-1">
                <i class="fas fa-flask me-2" style="color: #1a4d6b;"></i>
                Bon de demande d'analyse
            </h1>
            <p class="text-muted mb-0">Formulaire de prescription d'examens de laboratoire</p>
        </div>
        <div>
            <a href="{{ route('medecin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-medical me-2"></i> Bon de demande d'analyse
                    <span class="badge bg-info ms-2">LS-FLT-001</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medecin.bons-analyse.store') }}" id="bonForm">
                        @csrf

                        <!-- ========================================== -->
                        <!-- 1. INFORMATIONS GÉNÉRALES DU PATIENT        -->
                        <!-- ========================================== -->
                        <div class="section-title">
                            <i class="fas fa-user me-2"></i> Informations générales du patient
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Patient</label>
                                <select name="id_patient" id="patientSelect" class="form-select select2" required>
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
                                            {{ $selectedPatient && $selectedPatient->id_patient == $patient->id_patient ? 'selected' : '' }}>
                                            {{ $patient->nom_complet }} - {{ $patient->numero_fiche }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Infos patient automatiques -->
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nom, Post-nom et Prénom</label>
                                <input type="text" id="patientNom" class="form-control" readonly placeholder="-- Patient non sélectionné --">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sexe</label>
                                <input type="text" id="patientSexe" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date de naissance</label>
                                <input type="text" id="patientDateNaissance" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" id="patientTelephone" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Hospitalisé(e)</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="hospitalise" id="hospitalise" class="form-check-input" value="1">
                                    <label class="form-check-label" for="hospitalise">Oui</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="pavillonGroup" style="display:none;">
                                <label class="form-label">Pavillon N°</label>
                                <input type="text" name="pavillon" class="form-control" placeholder="N° Pavillon">
                            </div>
                            <div class="col-md-3" id="chambreGroup" style="display:none;">
                                <label class="form-label">Chambre N°</label>
                                <input type="text" name="chambre" class="form-control" placeholder="N° Chambre">
                            </div>
                            <div class="col-md-3" id="litGroup" style="display:none;">
                                <label class="form-label">Lit N°</label>
                                <input type="text" name="lit" class="form-control" placeholder="N° Lit">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Adresse du domicile</label>
                                <input type="text" name="adresse_domicile" id="patientAdresse" class="form-control" placeholder="Adresse du patient">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 2. MÉDECIN/CLINICIEN DEMANDEUR               -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-user-md me-2"></i> Médecin/Clinicién demandeur
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nom et prénom</label>
                                <input type="text" class="form-control" value="Dr. {{ Auth::user()->nom_complet }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="date_demande" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heure</label>
                                <input type="time" name="heure_demande" class="form-control" value="{{ date('H:i') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Service</label>
                                <input type="text" name="service_demandeur" class="form-control" placeholder="Médecine Générale" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone_demandeur" class="form-control" placeholder="Téléphone" required>
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
                            <div class="col-md-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" placeholder="À remplir par le labo" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" placeholder="À remplir par le labo" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date prélèvement</label>
                                <input type="text" class="form-control" placeholder="--/--/----" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heure</label>
                                <input type="text" class="form-control" placeholder="--:--" readonly style="background:#f8f9fa;">
                            </div>
                            <div class="col-md-2">
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
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="sang" value="sang" class="form-check-input">
                                        <label class="form-check-label" for="sang">Sang</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="selles" value="selles" class="form-check-input">
                                        <label class="form-check-label" for="selles">Selles</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="urines" value="urines" class="form-check-input">
                                        <label class="form-check-label" for="urines">Urines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="lcr" value="lcr" class="form-check-input">
                                        <label class="form-check-label" for="lcr">LCR</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="crachat" value="crachat" class="form-check-input">
                                        <label class="form-check-label" for="crachat">Crachat</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type_echantillon" id="autres" value="autres" class="form-check-input">
                                        <label class="form-check-label" for="autres">Autres</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <input type="text" name="autres_echantillon" class="form-control" placeholder="Précisez si autres...">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 5. EXAMENS PAR CATÉGORIE                   -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-microscope me-2"></i> Examens demandés
                        </div>

                        <div class="row">
                            <!-- Parasitologie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🦠 Parasitologie</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($examens['parasitologie'] as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][{{ $key }}]" 
                                                   value="1" id="para_{{ $key }}" class="form-check-input">
                                            <label class="form-check-label" for="para_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Hématologie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🩸 Hématologie</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($examens['hematologie'] as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][{{ $key }}]" 
                                                   value="1" id="hemato_{{ $key }}" class="form-check-input">
                                            <label class="form-check-label" for="hemato_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Microbiologie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🧫 Microbiologie</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($examens['microbiologie'] as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][{{ $key }}]" 
                                                   value="1" id="micro_{{ $key }}" class="form-check-input">
                                            <label class="form-check-label" for="micro_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Biochimie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🧪 Biochimie</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($examens['biochimie'] as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][{{ $key }}]" 
                                                   value="1" id="bio_{{ $key }}" class="form-check-input">
                                            <label class="form-check-label" for="bio_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Immuno-sérologie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🧬 Immuno-sérologie</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($examens['immuno_serologie'] as $key => $label)
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][{{ $key }}]" 
                                                   value="1" id="immuno_{{ $key }}" class="form-check-input">
                                            <label class="form-check-label" for="immuno_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Autres -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">📋 Autres analyses</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <input type="text" name="examens[autres][0]" class="form-control mb-2" placeholder="Autre analyse 1">
                                            <input type="text" name="examens[autres][1]" class="form-control mb-2" placeholder="Autre analyse 2">
                                            <input type="text" name="examens[autres][2]" class="form-control" placeholder="Autre analyse 3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 6. PRIORITÉ                                 -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-flag me-2"></i> Priorité
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input type="radio" name="priorite" id="normal" value="normal" checked class="form-check-input">
                                        <label class="form-check-label" for="normal">
                                            <span class="badge bg-primary">Normal</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="priorite" id="urgent" value="urgent" class="form-check-input">
                                        <label class="form-check-label" for="urgent">
                                            <span class="badge bg-danger">Urgent</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 7. INFORMATIONS CLINIQUES                  -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-notes-medical me-2"></i> Informations cliniques
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <textarea name="observations_cliniques" class="form-control" rows="3" 
                                          placeholder="Observations cliniques pertinentes..."></textarea>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 8. ACTIONS                                 -->
                        <!-- ========================================== -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i> Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i> Envoyer au laboratoire
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
        // Initialisation Select2
        $('.select2').select2({
            theme: 'bootstrap5',
            width: '100%'
        });

        // Afficher les infos du patient sélectionné
        $('#patientSelect').on('change', function() {
            var option = $(this).find('option:selected');
            var nom = option.data('nom') || '';
            var postnom = option.data('postnom') || '';
            var prenom = option.data('prenom') || '';
            var sexe = option.data('sexe') || '';
            var dateNaiss = option.data('date_naissance') || '';
            var telephone = option.data('telephone') || '';
            var adresse = option.data('adresse') || '';

            $('#patientNom').val(prenom + ' ' + nom + ' ' + postnom);
            $('#patientSexe').val(sexe == 'M' ? 'Masculin' : (sexe == 'F' ? 'Féminin' : ''));
            $('#patientDateNaissance').val(dateNaiss);
            $('#patientTelephone').val(telephone);
            $('#patientAdresse').val(adresse);
        });

        // Afficher/masquer les champs d'hospitalisation
        $('#hospitalise').on('change', function() {
            if ($(this).is(':checked')) {
                $('#pavillonGroup, #chambreGroup, #litGroup').show();
            } else {
                $('#pavillonGroup, #chambreGroup, #litGroup').hide();
            }
        });

        // Déclencher l'événement si un patient est pré-sélectionné
        if ($('#patientSelect').val()) {
            $('#patientSelect').trigger('change');
        }
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
    .card-header h6 {
        margin-bottom: 0;
        color: #1a4d6b;
    }
</style>
@endpush
@endsection