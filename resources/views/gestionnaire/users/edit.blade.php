@extends('layouts.gestionnaire')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-user-edit me-2" style="color: var(--secondary);"></i>
                Modifier l'utilisateur
            </h1>
            <p class="text-muted mb-0">{{ $user->prenom }} {{ $user->nom }} ({{ $user->matricule }})</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('gestionnaire.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations personnelles
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('gestionnaire.users.update', $user->id_user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-id-card me-1"></i> Matricule</label>
                                <input type="text" class="form-control" value="{{ $user->matricule }}" disabled>
                                <small class="text-muted">Le matricule ne peut pas être modifié</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-venus-mars me-1"></i> Sexe *</label>
                                <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="M" {{ old('sexe', $user->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe', $user->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><i class="fas fa-user me-1"></i> Nom *</label>
                                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                       value="{{ old('nom', $user->nom) }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><i class="fas fa-user me-1"></i> Postnom *</label>
                                <input type="text" name="postnom" class="form-control @error('postnom') is-invalid @enderror" 
                                       value="{{ old('postnom', $user->postnom) }}" required>
                                @error('postnom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><i class="fas fa-user me-1"></i> Prénom *</label>
                                <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" 
                                       value="{{ old('prenom', $user->prenom) }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-envelope me-1"></i> Email *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-phone me-1"></i> Téléphone *</label>
                                <input type="tel" name="telephone" class="form-control @error('telephone') is-invalid @enderror" 
                                       value="{{ old('telephone', $user->telephone) }}" required>
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-location-dot me-1"></i> Adresse *</label>
                            <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror" 
                                   value="{{ old('adresse', $user->adresse) }}" required>
                            @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-briefcase me-1"></i> Rôle *</label>
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_role }}" {{ old('role_id', $user->role_id) == $role->id_role ? 'selected' : '' }}>
                                            {{ $role->nom_role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3" id="specialite_group" style="display: {{ old('role_id', $user->role_id) == 2 ? 'block' : 'none' }}">
                                <label class="form-label"><i class="fas fa-stethoscope me-1"></i> Spécialité</label>
                                <select name="specialite" class="form-select">
                                    <option value="">Sélectionnez une spécialité</option>
                                    <option value="Médecine Générale" {{ old('specialite', $user->specialite) == 'Médecine Générale' ? 'selected' : '' }}>Médecine Générale</option>
                                    <option value="Pédiatrie" {{ old('specialite', $user->specialite) == 'Pédiatrie' ? 'selected' : '' }}>Pédiatrie</option>
                                    <option value="Cardiologie" {{ old('specialite', $user->specialite) == 'Cardiologie' ? 'selected' : '' }}>Cardiologie</option>
                                    <option value="Neurologie" {{ old('specialite', $user->specialite) == 'Neurologie' ? 'selected' : '' }}>Neurologie</option>
                                    <option value="Gynécologie" {{ old('specialite', $user->specialite) == 'Gynécologie' ? 'selected' : '' }}>Gynécologie</option>
                                    <option value="Chirurgie" {{ old('specialite', $user->specialite) == 'Chirurgie' ? 'selected' : '' }}>Chirurgie</option>
                                    <option value="Pneumologie" {{ old('specialite', $user->specialite) == 'Pneumologie' ? 'selected' : '' }}>Pneumologie</option>
                                    <option value="Dermatologie" {{ old('specialite', $user->specialite) == 'Dermatologie' ? 'selected' : '' }}>Dermatologie</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="est_actif" class="form-check-input" value="1" 
                                       {{ old('est_actif', $user->est_actif) ? 'checked' : '' }}>
                                <label class="form-check-label">Compte actif</label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <!-- ✅ FORMULAIRE POST pour réinitialiser -->
                            <form action="{{ route('gestionnaire.users.reset-password', $user->id_user) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->prenom }} {{ $user->nom }} ?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="fas fa-key me-2"></i> Réinitialiser mot de passe
                                </button>
                            </form>
                            
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
    document.getElementById('role_id').addEventListener('change', function() {
        var specialiteGroup = document.getElementById('specialite_group');
        if (this.value == '2') {
            specialiteGroup.style.display = 'block';
        } else {
            specialiteGroup.style.display = 'none';
        }
    });
</script>
@endpush
@endsection