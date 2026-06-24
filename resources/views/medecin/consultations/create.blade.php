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
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nom, Post-nom et Prénom <span class="text-danger">*</span></label>
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
                            
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Sexe <span class="text-danger">*</span></label>
                                <select name="sexe" id="patient_sexe_select" class="form-select" required>
                                    <option value="">-- Sélectionnez --</option>
                                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date naissance <span class="text-danger">*</span></label>
                                <input type="date" name="date_naissance" id="patient_date_naissance_input" class="form-control @error('date_naissance') is-invalid @enderror" 
                                       value="{{ old('date_naissance') }}" required>
                                @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" name="telephone" id="patient_telephone_input" class="form-control @error('telephone') is-invalid @enderror" 
                                       value="{{ old('telephone') }}" placeholder="+243 XX XXX XXXX" required>
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- ⭐ HOSPITALISATION AVEC CHAMBRE OBLIGATOIRE ⭐ -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Hospitalisé(e)</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="hospitalise" id="hospitalise" class="form-check-input" value="1" {{ old('hospitalise') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hospitalise">Oui</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="pavillonGroup" style="{{ old('hospitalise') ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Pavillon <span class="text-danger">*</span></label>
                                <input type="text" name="pavillon" class="form-control" placeholder="N° Pavillon" value="{{ old('pavillon') }}" {{ old('hospitalise') ? 'required' : '' }}>
                            </div>
                            <div class="col-md-3" id="chambreGroup" style="{{ old('hospitalise') ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Chambre <span class="text-danger">*</span></label>
                                <input type="text" name="chambre" class="form-control" placeholder="N° Chambre" value="{{ old('chambre') }}" {{ old('hospitalise') ? 'required' : '' }}>
                            </div>
                            <div class="col-md-3" id="litGroup" style="{{ old('hospitalise') ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Lit</label>
                                <input type="text" name="lit" class="form-control" placeholder="N° Lit" value="{{ old('lit') }}">
                            </div>
                        </div>

                        <!-- Adresse du domicile -->
                        <div class="row mt-2">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Adresse du domicile</label>
                                <input type="text" name="adresse_domicile" id="patient_adresse" class="form-control" placeholder="Adresse du patient" value="{{ old('adresse_domicile') }}">
                            </div>
                        </div>

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
                                <input type="text" name="service_demandeur" class="form-control" placeholder="Médecine Générale" value="{{ old('service_demandeur') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone_demandeur" class="form-control" placeholder="Téléphone" value="{{ old('telephone_demandeur') }}">
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
                            <!-- Parasitologie -->
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">🦠 Parasitologie</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][culot_urines]" value="1" id="para_culot" class="form-check-input" {{ old('examens.parasitologie.culot_urines') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_culot">Culot Urines</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][sucre_urines]" value="1" id="para_sucre" class="form-check-input" {{ old('examens.parasitologie.sucre_urines') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_sucre">Sucre Urines</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][albuminurie]" value="1" id="para_albumine" class="form-check-input" {{ old('examens.parasitologie.albuminurie') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_albumine">Albuminurie</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][ph_urines]" value="1" id="para_ph" class="form-check-input" {{ old('examens.parasitologie.ph_urines') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_ph">pH Urines</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][acetone_urines]" value="1" id="para_acetone" class="form-check-input" {{ old('examens.parasitologie.acetone_urines') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_acetone">Acétone Urines</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][selles_frais]" value="1" id="para_selles" class="form-check-input" {{ old('examens.parasitologie.selles_frais') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_selles">Selles à frais</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][h_pylori]" value="1" id="para_pylori" class="form-check-input" {{ old('examens.parasitologie.h_pylori') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_pylori">H. Pylori</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][tdr_malaria]" value="1" id="para_malaria" class="form-check-input" {{ old('examens.parasitologie.tdr_malaria') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_malaria">TDR Malaria</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][goutte_epaisse]" value="1" id="para_goutte_epaisse" class="form-check-input" {{ old('examens.parasitologie.goutte_epaisse') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_goutte_epaisse">Goutte Épaisse</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][goutte_fraiche]" value="1" id="para_goutte_fraiche" class="form-check-input" {{ old('examens.parasitologie.goutte_fraiche') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_goutte_fraiche">Goutte Fraîche</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[parasitologie][trypanosome]" value="1" id="para_trypanosome" class="form-check-input" {{ old('examens.parasitologie.trypanosome') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="para_trypanosome">Recherche Trypanosome</label>
                                        </div>
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
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][gs]" value="1" id="hemato_gs" class="form-check-input" {{ old('examens.hematologie.gs') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_gs">GS</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][rh]" value="1" id="hemato_rh" class="form-check-input" {{ old('examens.hematologie.rh') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_rh">Rh</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][hct]" value="1" id="hemato_hct" class="form-check-input" {{ old('examens.hematologie.hct') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_hct">Hct</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][gb]" value="1" id="hemato_gb" class="form-check-input" {{ old('examens.hematologie.gb') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_gb">GB</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][gr]" value="1" id="hemato_gr" class="form-check-input" {{ old('examens.hematologie.gr') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_gr">GR</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][fl]" value="1" id="hemato_fl" class="form-check-input" {{ old('examens.hematologie.fl') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_fl">FL</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][vs]" value="1" id="hemato_vs" class="form-check-input" {{ old('examens.hematologie.vs') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_vs">VS</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][plt]" value="1" id="hemato_plt" class="form-check-input" {{ old('examens.hematologie.plt') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_plt">PLT</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][reticulocytes]" value="1" id="hemato_reticulocytes" class="form-check-input" {{ old('examens.hematologie.reticulocytes') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_reticulocytes">Réticulocytes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][vgm]" value="1" id="hemato_vgm" class="form-check-input" {{ old('examens.hematologie.vgm') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_vgm">VGM</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][ccmh]" value="1" id="hemato_ccmh" class="form-check-input" {{ old('examens.hematologie.ccmh') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_ccmh">CCMH</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[hematologie][tcmh]" value="1" id="hemato_tcmh" class="form-check-input" {{ old('examens.hematologie.tcmh') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hemato_tcmh">TCMH</label>
                                        </div>
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
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][gram_ponctions]" value="1" id="micro_gram" class="form-check-input" {{ old('examens.microbiologie.gram_ponctions') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="micro_gram">Gram-Ponctions</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][cytologie]" value="1" id="micro_cytologie" class="form-check-input" {{ old('examens.microbiologie.cytologie') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="micro_cytologie">Cytologie</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][rivalta]" value="1" id="micro_rivalta" class="form-check-input" {{ old('examens.microbiologie.rivalta') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="micro_rivalta">Rivalta</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][ziehl]" value="1" id="micro_ziehl" class="form-check-input" {{ old('examens.microbiologie.ziehl') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="micro_ziehl">Ziehl</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[microbiologie][elements_gram]" value="1" id="micro_elements" class="form-check-input" {{ old('examens.microbiologie.elements_gram') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="micro_elements">Éléments Gram</label>
                                        </div>
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
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][glycemie]" value="1" id="bio_glycemie" class="form-check-input" {{ old('examens.biochimie.glycemie') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_glycemie">Glycémie</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][uree]" value="1" id="bio_uree" class="form-check-input" {{ old('examens.biochimie.uree') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_uree">Urée</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][creatinine]" value="1" id="bio_creatinine" class="form-check-input" {{ old('examens.biochimie.creatinine') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_creatinine">Créatinine</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][cholesterol_total]" value="1" id="bio_cholesterol" class="form-check-input" {{ old('examens.biochimie.cholesterol_total') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_cholesterol">Cholestérol Total</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][triglycerides]" value="1" id="bio_triglycerides" class="form-check-input" {{ old('examens.biochimie.triglycerides') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_triglycerides">Triglycérides</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][hdl]" value="1" id="bio_hdl" class="form-check-input" {{ old('examens.biochimie.hdl') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_hdl">HDL-Cholestérol</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][ldl]" value="1" id="bio_ldl" class="form-check-input" {{ old('examens.biochimie.ldl') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_ldl">LDL-Cholestérol</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[biochimie][acide_urique]" value="1" id="bio_acide" class="form-check-input" {{ old('examens.biochimie.acide_urique') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bio_acide">Acide urique</label>
                                        </div>
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
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][aghbs]" value="1" id="immuno_aghbs" class="form-check-input" {{ old('examens.immuno_serologie.aghbs') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="immuno_aghbs">AgHBS</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][achcv]" value="1" id="immuno_achcv" class="form-check-input" {{ old('examens.immuno_serologie.achcv') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="immuno_achcv">AcHCV</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][rpr_vdrl]" value="1" id="immuno_rpr" class="form-check-input" {{ old('examens.immuno_serologie.rpr_vdrl') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="immuno_rpr">RPR/VDRL</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][aslo]" value="1" id="immuno_aslo" class="form-check-input" {{ old('examens.immuno_serologie.aslo') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="immuno_aslo">ASLO</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="examens[immuno_serologie][crp]" value="1" id="immuno_crp" class="form-check-input" {{ old('examens.immuno_serologie.crp') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="immuno_crp">CRP</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 6. SIGNES VITAUX                           -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-heartbeat me-2"></i> Signes vitaux
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tension artérielle</label>
                                <input type="text" name="tension_arterielle" class="form-control" placeholder="120/80" value="{{ old('tension_arterielle') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Poids (kg)</label>
                                <input type="number" step="0.1" name="poids" class="form-control" placeholder="70.5" value="{{ old('poids') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Taille (cm)</label>
                                <input type="number" step="0.1" name="taille" class="form-control" placeholder="170" value="{{ old('taille') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Température (°C)</label>
                                <input type="number" step="0.1" name="temperature" class="form-control" placeholder="37.0" value="{{ old('temperature') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Fréquence cardiaque</label>
                                <input type="number" name="frequence_cardiaque" class="form-control" placeholder="72 bpm" value="{{ old('frequence_cardiaque') }}">
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 7. CONSULTATION                            -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-stethoscope me-2"></i> Détails de la consultation
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Motif de la consultation <span class="text-danger">*</span></label>
                                <textarea name="motif" rows="2" class="form-control @error('motif') is-invalid @enderror" placeholder="Raison de la consultation..." required>{{ old('motif') }}</textarea>
                                @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Symptômes <span class="text-danger">*</span></label>
                                <textarea name="symptomes" rows="3" class="form-control @error('symptomes') is-invalid @enderror" placeholder="Liste des symptômes..." required>{{ old('symptomes') }}</textarea>
                                @error('symptomes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Diagnostic <span class="text-danger">*</span></label>
                                <textarea name="diagnostic" rows="3" class="form-control @error('diagnostic') is-invalid @enderror" placeholder="Diagnostic médical..." required>{{ old('diagnostic') }}</textarea>
                                @error('diagnostic') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prescription</label>
                                <textarea name="prescription" rows="3" class="form-control" placeholder="Médicaments, posologie, durée...">{{ old('prescription') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observations</label>
                                <textarea name="observations_medecin" rows="3" class="form-control" placeholder="Notes et observations supplémentaires...">{{ old('observations_medecin') }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date consultation</label>
                                <input type="date" name="date_consultation" class="form-control @error('date_consultation') is-invalid @enderror" value="{{ old('date_consultation', date('Y-m-d')) }}">
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
                        </div>

                        <!-- ========================================== -->
                        <!-- 8. PRIORITÉ                                 -->
                        <!-- ========================================== -->
                        <div class="section-title mt-4">
                            <i class="fas fa-flag me-2"></i> Priorité
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input type="radio" name="priorite" id="normal" value="normal" class="form-check-input" {{ old('priorite', 'normal') == 'normal' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="normal">
                                            <span class="badge bg-primary">Normal</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="priorite" id="moyen" value="moyen" class="form-check-input" {{ old('priorite') == 'moyen' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="moyen">
                                            <span class="badge bg-warning">Moyen</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="priorite" id="urgent" value="urgent" class="form-check-input" {{ old('priorite') == 'urgent' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="urgent">
                                            <span class="badge bg-danger">Urgent</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 9. ACTIONS                                -->
                        <!-- ========================================== -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Un numéro de consultation et un bon d'analyse seront générés automatiquement.
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
        // Initialisation Select2
        $('.select2').select2({
            theme: 'bootstrap5',
            width: '100%'
        });

        // Afficher les infos du patient sélectionné
        $('#patient_select').on('change', function() {
            var option = $(this).find('option:selected');
            var sexe = option.data('sexe') || '';
            var dateNaiss = option.data('date_naissance') || '';
            var telephone = option.data('telephone') || '';
            var adresse = option.data('adresse') || '';

            if (sexe) $('#patient_sexe_select').val(sexe);
            if (dateNaiss) $('#patient_date_naissance_input').val(dateNaiss);
            if (telephone) $('#patient_telephone_input').val(telephone);
            if (adresse) $('#patient_adresse').val(adresse);
        });

        // ⭐ AFFICHER/MASQUER LES CHAMPS D'HOSPITALISATION AVEC OBLIGATION ⭐
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

        // Déclencher l'événement si un patient est pré-sélectionné ou si hospitalisation cochée
        if ($('#patient_select').val()) {
            $('#patient_select').trigger('change');
        }
        
        // Vérifier l'état initial de l'hospitalisation
        if ($('#hospitalise').is(':checked')) {
            $('#pavillonGroup').show();
            $('#chambreGroup').show();
            $('#litGroup').show();
            $('#pavillonGroup input, #chambreGroup input').prop('required', true);
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
    #pavillonGroup, #chambreGroup, #litGroup {
        transition: all 0.3s ease;
        display: none;
    }
</style>
@endpush
@endsection