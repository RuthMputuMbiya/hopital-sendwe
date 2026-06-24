@extends('layouts.gestionnaire')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-user-plus me-2" style="color: var(--secondary);"></i>
                Ajouter un utilisateur
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} | 
                <i class="fas fa-clock ms-2 me-1"></i> 
                {{ now()->format('H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2" data-aos="fade-left">
            <a href="{{ route('gestionnaire.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto" data-aos="fade-up">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="fas fa-id-card text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">Informations personnelles</h5>
                            <small class="text-muted">Veuillez remplir tous les champs obligatoires</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('gestionnaire.users.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-1 me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-uppercase small text-primary">Identité</h6>
                                <hr class="flex-grow-1 ms-3 mb-0">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-id-card me-1 text-primary"></i> Matricule <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror" 
                                           value="{{ old('matricule') }}" placeholder="Ex: DOC001, REC001, LAB001" required>
                                    @error('matricule') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1 text-primary"></i> Nom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                           value="{{ old('nom') }}" placeholder="Nom de famille" required>
                                    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1 text-primary"></i> Postnom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="postnom" class="form-control @error('postnom') is-invalid @enderror" 
                                           value="{{ old('postnom') }}" placeholder="Postnom" required>
                                    @error('postnom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user me-1 text-primary"></i> Prénom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" 
                                           value="{{ old('prenom') }}" placeholder="Prénom" required>
                                    @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-venus-mars me-1 text-primary"></i> Sexe <span class="text-danger">*</span>
                                    </label>
                                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                        <option value="">Sélectionnez</option>
                                        <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                    @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-1 me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-address-card text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-uppercase small text-primary">Contact</h6>
                                <hr class="flex-grow-1 ms-3 mb-0">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-1 text-primary"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" placeholder="exemple@hopital.cd" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-phone me-1 text-primary"></i> Téléphone <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="telephone" class="form-control @error('telephone') is-invalid @enderror" 
                                           value="{{ old('telephone') }}" placeholder="+243 XX XXX XXXX" required>
                                    @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-location-dot me-1 text-primary"></i> Adresse <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror" 
                                           value="{{ old('adresse') }}" placeholder="Lubumbashi, Quartier, Avenue" required>
                                    @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-1 me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-briefcase text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-uppercase small text-primary">Fonction</h6>
                                <hr class="flex-grow-1 ms-3 mb-0">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-briefcase me-1 text-primary"></i> Rôle <span class="text-danger">*</span>
                                    </label>
                                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                        <option value="">Sélectionnez un rôle</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id_role }}" {{ old('role_id') == $role->id_role ? 'selected' : '' }}>
                                                {{ $role->nom_role }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6" id="specialite_group" style="display: none;">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-stethoscope me-1 text-primary"></i> Spécialité <span class="text-danger">*</span>
                                    </label>
                                    <select name="specialite" class="form-select">
                                        <option value="">Sélectionnez une spécialité</option>
                                        <option value="Médecine Générale">Médecine Générale</option>
                                        <option value="Pédiatrie">Pédiatrie</option>
                                        <option value="Cardiologie">Cardiologie</option>
                                        <option value="Neurologie">Neurologie</option>
                                        <option value="Gynécologie">Gynécologie</option>
                                        <option value="Chirurgie">Chirurgie</option>
                                        <option value="Pneumologie">Pneumologie</option>
                                        <option value="Dermatologie">Dermatologie</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-1 me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-toggle-on text-white fa-sm"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold text-uppercase small text-primary">Statut du compte</h6>
                                <hr class="flex-grow-1 ms-3 mb-0">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="est_actif" class="form-check-input" id="est_actif" value="1" checked style="width: 40px; height: 20px;">
                                        <label class="form-check-label ms-2 fw-semibold" for="est_actif">
                                            <span class="badge bg-success">Compte actif</span>
                                        </label>
                                        <small class="text-muted d-block mt-1">Le compte sera immédiatement accessible par l'utilisateur</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fas fa-key fa-2x me-3 text-primary"></i>
                            <div>
                                <strong class="d-block">Mot de passe temporaire</strong>
                                <span>Le mot de passe initial sera : <code class="fw-bold">Password@123</code></span>
                                <br><small>L'utilisateur devra changer son mot de passe à la première connexion.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <button type="reset" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-undo me-2"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Enregistrer l'utilisateur
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
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_id');
        const specialiteGroup = document.getElementById('specialite_group');
        const specialiteSelect = document.querySelector('select[name="specialite"]');
        
        function toggleSpecialite() {
            if (roleSelect.value == '2') {
                specialiteGroup.style.display = 'block';
                specialiteSelect.required = true;
            } else {
                specialiteGroup.style.display = 'none';
                specialiteSelect.required = false;
            }
        }
        
        roleSelect.addEventListener('change', toggleSpecialite);
        toggleSpecialite();
    });
</script>
@endpush
@endsection