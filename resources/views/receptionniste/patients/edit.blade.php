@extends('layouts.receptionniste')

@section('title', 'Modifier patient')

@section('page-title', 'Modifier le patient')
@section('page-subtitle', $patient->nom_complet . ' (' . $patient->numero_fiche . ')')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-white border-0 py-4 rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="fas fa-user-edit text-warning fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-warning fs-4">Modification du patient</h5>
                                <small class="text-muted">Mettez à jour les informations du patient</small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('receptionniste.patients.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5 pt-0">
                    <form method="POST" action="{{ route('receptionniste.patients.update', $patient->id_patient) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- ========== SECTION 1: IDENTITÉ ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-warning rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-warning tracking-wider">Identité du patient</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-warning opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Numéro fiche</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-qrcode text-warning"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" value="{{ $patient->numero_fiche }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Sexe <span class="text-danger">*</span></label>
                                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                        <option value="M" {{ old('sexe', $patient->sexe) == 'M' ? 'selected' : '' }}>👨 Masculin</option>
                                        <option value="F" {{ old('sexe', $patient->sexe) == 'F' ? 'selected' : '' }}>👩 Féminin</option>
                                    </select>
                                    @error('sexe') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Date naissance <span class="text-danger">*</span></label>
                                    <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" 
                                           value="{{ old('date_naissance', $patient->date_naissance->format('Y-m-d')) }}" required>
                                    @error('date_naissance') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Âge</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-warning"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" value="{{ $patient->age }} ans" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Nom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-warning"></i></span>
                                        <input type="text" name="nom" class="form-control border-start-0 @error('nom') is-invalid @enderror" 
                                               value="{{ old('nom', $patient->nom) }}" required>
                                    </div>
                                    @error('nom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Postnom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-warning"></i></span>
                                        <input type="text" name="postnom" class="form-control border-start-0 @error('postnom') is-invalid @enderror" 
                                               value="{{ old('postnom', $patient->postnom) }}" required>
                                    </div>
                                    @error('postnom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Prénom <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-warning"></i></span>
                                        <input type="text" name="prenom" class="form-control border-start-0 @error('prenom') is-invalid @enderror" 
                                               value="{{ old('prenom', $patient->prenom) }}" required>
                                    </div>
                                    @error('prenom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Lieu naissance</label>
                                    <input type="text" name="lieu_naissance" class="form-control" value="{{ old('lieu_naissance', $patient->lieu_naissance) }}" placeholder="Ex: Lubumbashi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Nationalité</label>
                                    <input type="text" name="nationalite" class="form-control" value="{{ old('nationalite', $patient->nationalite) }}" placeholder="Ex: Congolaise">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Profession</label>
                                    <input type="text" name="profession" class="form-control" value="{{ old('profession', $patient->profession) }}" placeholder="Profession">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Situation matrimoniale</label>
                                    <select name="situation_matrimoniale" class="form-select">
                                        <option value="">Sélectionnez</option>
                                        <option value="Célibataire" {{ old('situation_matrimoniale', $patient->situation_matrimoniale) == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                        <option value="Marié(e)" {{ old('situation_matrimoniale', $patient->situation_matrimoniale) == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                        <option value="Divorcé(e)" {{ old('situation_matrimoniale', $patient->situation_matrimoniale) == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                        <option value="Veuf/Veuve" {{ old('situation_matrimoniale', $patient->situation_matrimoniale) == 'Veuf/Veuve' ? 'selected' : '' }}>Veuf/Veuve</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION 2: CONTACT ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-warning rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-address-card text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-warning tracking-wider">Coordonnées</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-warning opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-warning"></i></span>
                                        <input type="tel" name="telephone" class="form-control border-start-0 @error('telephone') is-invalid @enderror" 
                                               value="{{ old('telephone', $patient->telephone) }}" required>
                                    </div>
                                    @error('telephone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone secondaire</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-warning"></i></span>
                                        <input type="tel" name="telephone_secondaire" class="form-control border-start-0" value="{{ old('telephone_secondaire', $patient->telephone_secondaire) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-warning"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $patient->email) }}" placeholder="exemple@email.com">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">Adresse <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-location-dot text-warning"></i></span>
                                        <input type="text" name="adresse" class="form-control border-start-0 @error('adresse') is-invalid @enderror" 
                                               value="{{ old('adresse', $patient->adresse) }}" required>
                                    </div>
                                    @error('adresse') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Ville</label>
                                    <input type="text" name="ville" class="form-control" value="{{ old('ville', $patient->ville) }}" placeholder="Lubumbashi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Quartier</label>
                                    <input type="text" name="quartier" class="form-control" value="{{ old('quartier', $patient->quartier) }}" placeholder="Quartier">
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION 3: CONTACT URGENCE ========== -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-warning rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-phone-alt text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-uppercase small text-warning tracking-wider">Personne à contacter en cas d'urgence</h6>
                                <div class="flex-grow-1 ms-3 border-bottom border-2 border-warning opacity-25"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">Nom complet</label>
                                    <input type="text" name="contact_urgence_nom" class="form-control" value="{{ old('contact_urgence_nom', $patient->contact_urgence_nom) }}" placeholder="Nom de la personne">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Téléphone</label>
                                    <input type="tel" name="contact_urgence_telephone" class="form-control" value="{{ old('contact_urgence_telephone', $patient->contact_urgence_telephone) }}" placeholder="+243 XX XXX XXXX">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Relation</label>
                                    <input type="text" name="contact_urgence_relation" class="form-control" value="{{ old('contact_urgence_relation', $patient->contact_urgence_relation) }}" placeholder="Père, Mère, Frère...">
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                            <a href="{{ route('receptionniste.patients.show', $patient->id_patient) }}" class="btn btn-outline-secondary px-5 py-2 rounded-pill">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning px-5 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .tracking-wider { letter-spacing: 1px; }
    .form-control, .form-select { border-radius: 12px; padding: 10px 15px; }
    .input-group-text { border-radius: 12px 0 0 12px; }
    .form-control:focus, .form-select:focus { border-color: var(--warning); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
</style>
@endpush
@endsection