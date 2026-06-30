@extends('layouts.receptionniste')

@section('title', 'Nouveau patient')

@section('page-title', 'Nouveau patient')
@section('page-subtitle', 'Enregistrer un nouveau patient dans le système')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-white border-0 py-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-user-plus text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary fs-4">Formulaire d'enregistrement patient</h5>
                            <small class="text-muted">Veuillez remplir tous les champs obligatoires (<span class="text-danger">*</span>)</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5 pt-0">
                    <form method="POST" action="{{ route('receptionniste.patients.store') }}" id="patientForm">
                        @csrf
                        
                        <!-- ========== SECTION 1: IDENTITÉ ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary tracking-wider">Identité du patient</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Nom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" name="nom" class="form-control border-start-0 @error('nom') is-invalid @enderror" 
                                               value="{{ old('nom') }}" placeholder="Nom de famille" required>
                                    </div>
                                    @error('nom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Postnom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" name="postnom" class="form-control border-start-0 @error('postnom') is-invalid @enderror" 
                                               value="{{ old('postnom') }}" placeholder="Postnom" required>
                                    </div>
                                    @error('postnom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Prénom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" name="prenom" class="form-control border-start-0 @error('prenom') is-invalid @enderror" 
                                               value="{{ old('prenom') }}" placeholder="Prénom" required>
                                    </div>
                                    @error('prenom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Sexe <span class="text-danger">*</span></label>
                                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                        <option value="">Sélectionnez</option>
                                        <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                    @error('sexe') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Date naissance <span class="text-danger">*</span></label>
                                    <input type="date" name="date_naissance" id="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" 
                                           value="{{ old('date_naissance') }}" required>
                                    @error('date_naissance') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Lieu naissance</label>
                                    <input type="text" name="lieu_naissance" class="form-control" value="{{ old('lieu_naissance') }}" placeholder="Ex: Lubumbashi">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Nationalité</label>
                                    <input type="text" name="nationalite" class="form-control" value="{{ old('nationalite') }}" placeholder="Ex: Congolaise">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Profession</label>
                                    <input type="text" name="profession" class="form-control" value="{{ old('profession') }}" placeholder="Profession du patient">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Situation matrimoniale</label>
                                    <select name="situation_matrimoniale" class="form-select">
                                        <option value="">Sélectionnez</option>
                                        <option value="Célibataire" {{ old('situation_matrimoniale') == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                        <option value="Marié(e)" {{ old('situation_matrimoniale') == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                        <option value="Divorcé(e)" {{ old('situation_matrimoniale') == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                        <option value="Veuf/Veuve" {{ old('situation_matrimoniale') == 'Veuf/Veuve' ? 'selected' : '' }}>Veuf/Veuve</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION 2: CONTACT ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-address-card text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary tracking-wider">Coordonnées</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-primary"></i></span>
                                        <input type="tel" name="telephone" class="form-control border-start-0 @error('telephone') is-invalid @enderror" 
                                               value="{{ old('telephone') }}" placeholder="+243 XX XXX XXXX" required>
                                    </div>
                                    @error('telephone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone secondaire</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-primary"></i></span>
                                        <input type="tel" name="telephone_secondaire" class="form-control border-start-0" value="{{ old('telephone_secondaire') }}" placeholder="+243 XX XXX XXXX">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}" placeholder="exemple@email.com">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">Adresse <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-location-dot text-primary"></i></span>
                                        <input type="text" name="adresse" class="form-control border-start-0 @error('adresse') is-invalid @enderror" 
                                               value="{{ old('adresse') }}" placeholder="Numéro et nom de rue" required>
                                    </div>
                                    @error('adresse') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Ville</label>
                                    <input type="text" name="ville" class="form-control" value="{{ old('ville') }}" placeholder="Lubumbashi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Quartier</label>
                                    <input type="text" name="quartier" class="form-control" value="{{ old('quartier') }}" placeholder="Quartier">
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION 3: CONTACT URGENCE ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-phone-alt text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary tracking-wider">Personne à contacter en cas d'urgence</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">Nom complet</label>
                                    <input type="text" name="contact_urgence_nom" class="form-control" value="{{ old('contact_urgence_nom') }}" placeholder="Nom de la personne">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone</label>
                                    <input type="tel" name="contact_urgence_telephone" class="form-control" value="{{ old('contact_urgence_telephone') }}" placeholder="+243 XX XXX XXXX">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Relation</label>
                                    <input type="text" name="contact_urgence_relation" class="form-control" value="{{ old('contact_urgence_relation') }}" placeholder="Père, Mère, Frère...">
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION 4: ATTRIBUTION MÉDECIN ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-md text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-primary tracking-wider">Attribution du médecin traitant</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                                    <i class="fas fa-stethoscope text-primary fs-5"></i>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-bold text-primary mb-0">Médecin attribué</label>
                                                    <small class="text-muted d-block">Sélectionnez le médecin qui suivra ce patient</small>
                                                </div>
                                            </div>
                                            
                                            <select name="medecin_attribue" class="form-select select2-doctor" id="medecin_select">
                                                <option value="">📋 Aucun (à attribuer plus tard)</option>
                                                @foreach($medecins as $medecin)
                                                    <option value="{{ $medecin->id_user }}" {{ old('medecin_attribue') == $medecin->id_user ? 'selected' : '' }}>
                                                         Dr. {{ $medecin->prenom }} {{ $medecin->nom }} 
                                                        @if($medecin->specialite)
                                                            - 🏥 {{ $medecin->specialite }}
                                                        @else
                                                            - 📋 Médecin généraliste
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            <div class="d-flex align-items-center mt-3 pt-2">
                                                <div class="rounded-circle bg-info bg-opacity-10 p-1 me-2">
                                                    <i class="fas fa-bell text-info fa-xs"></i>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-bell me-1 text-primary"></i> 
                                                    Une notification sera affichée sur le tableau de bord du médecin
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4" id="medecinInfoCard" style="display: none;">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
                                        <div class="card-body p-4 text-center">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-flex mb-3">
                                                <i class="fas fa-check-circle text-success fa-2x"></i>
                                            </div>
                                            <h6 class="fw-bold text-success mb-2">Médecin sélectionné</h6>
                                            <div id="medecinInfo" class="small">
                                                <!-- Info médecin s'affichera ici -->
                                            </div>
                                            <div class="mt-3">
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                                    <i class="fas fa-bell me-1"></i> Notification au tableau de bord
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert numéro fiche -->
                        <div class="alert alert-custom bg-info bg-opacity-10 border-0 rounded-4 p-4 mb-5">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-qrcode fa-3x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <strong class="d-block text-info fs-6">Numéro de fiche automatique</strong>
                                    <span class="text-muted small">Un numéro de fiche unique sera généré automatiquement lors de l'enregistrement.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                            <a href="{{ route('receptionniste.patients.index') }}" class="btn btn-outline-secondary px-5 py-2 rounded-pill">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="reset" class="btn btn-outline-warning px-5 py-2 rounded-pill">
                                <i class="fas fa-undo me-2"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Enregistrer le patient
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
        // Initialisation Select2
        $('.select2-doctor').select2({
            theme: 'bootstrap5',
            width: '100%',
            placeholder: 'Rechercher un médecin...',
            allowClear: true
        });
        
        // Gestion de l'affichage du médecin sélectionné
        const medecinSelect = $('#medecin_select');
        const medecinInfoCard = $('#medecinInfoCard');
        const medecinInfo = $('#medecinInfo');
        
        medecinSelect.on('change', function() {
            const selectedOption = medecinSelect.find('option:selected');
            const medecinId = $(this).val();
            
            if (medecinId && medecinId !== '') {
                const medecinText = selectedOption.text().trim();
                medecinInfo.html(`
                    <div class="text-start">
                        <div class="mb-2">${medecinText}</div>
                        <hr class="my-2">
                        <small class="text-muted">
                            <i class="fas fa-bell me-1"></i> Notifié lors de l'enregistrement
                        </small>
                    </div>
                `);
                medecinInfoCard.show(300);
            } else {
                medecinInfoCard.hide(300);
            }
        });
        
        // Déclencher l'événement si un médecin est pré-sélectionné
        if (medecinSelect.val() && medecinSelect.val() !== '') {
            medecinSelect.trigger('change');
        }
        
        // Ajouter un effet de confirmation avant soumission
        $('#patientForm').on('submit', function(e) {
            const nom = $('input[name="nom"]').val();
            const prenom = $('input[name="prenom"]').val();
            
            if (nom && prenom) {
                return confirm(`Confirmer l'enregistrement du patient ${prenom} ${nom} ?`);
            }
            return true;
        });
    });
</script>
@endpush

@push('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #2c6e9e;
        box-shadow: 0 0 0 3px rgba(44, 110, 158, 0.1);
    }
    .card {
        border-radius: 16px !important;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 1px solid #e9ecef !important;
    }
    .input-group-text {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-right: none;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group .form-control:focus {
        border-color: #2c6e9e;
    }
    .btn {
        border-radius: 50px !important;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: linear-gradient(135deg, #1a4d8c, #2c6e9e);
        border: none;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #0f2b3d, #1a4d8c);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(26, 77, 140, 0.3);
    }
    .btn-outline-secondary:hover {
        background: #f1f3f5;
    }
    .btn-outline-warning:hover {
        background: #fff3e0;
    }
    .alert-custom {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border-left: 4px solid #0284c7;
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
    }
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .select2-container--bootstrap5 .select2-selection {
        min-height: 46px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 5px 10px;
    }
    .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        font-size: 14px;
    }
    .select2-container--bootstrap5 .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
    }
    .select2-container--bootstrap5 .select2-dropdown {
        border-radius: 10px !important;
        border: 1.5px solid #e2e8f0 !important;
    }
    .select2-container--bootstrap5 .select2-results__option {
        padding: 8px 14px;
        font-size: 14px;
    }
    .select2-container--bootstrap5 .select2-results__option--highlighted {
        background: linear-gradient(135deg, #1a4d8c, #2c6e9e) !important;
    }
    @media (max-width: 768px) {
        .card-body {
            padding: 20px !important;
        }
        .form-control, .form-select {
            font-size: 13px;
            padding: 8px 12px;
        }
        .btn {
            font-size: 13px;
            padding: 8px 16px;
        }
    }
</style>
@endpush
@endsection