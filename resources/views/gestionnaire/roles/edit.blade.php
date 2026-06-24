@extends('layouts.gestionnaire')

@section('title', 'Modifier le rôle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-edit me-2" style="color: var(--secondary);"></i>
                Modifier le rôle : {{ $role->nom_role }}
            </h1>
            <p class="text-muted mb-0">Modifiez les informations du rôle</p>
        </div>
        <div data-aos="fade-left">
            <div class="d-flex gap-2">
                <a href="{{ route('gestionnaire.roles.index') }}" class="btn btn-outline-secondary">
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
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto" data-aos="fade-up">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                            <i class="fas fa-edit text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">Informations du rôle</h5>
                            <small class="text-muted">Modifiez les détails du rôle</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('gestionnaire.roles.update', $role->id_role) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hashtag me-1 text-primary"></i> ID du rôle
                            </label>
                            <div class="bg-light p-3 rounded">
                                <code class="fw-bold">#{{ $role->id_role }}</code>
                            </div>
                            <small class="text-muted">L'ID ne peut pas être modifié</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i> Nom du rôle <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom_role" class="form-control @error('nom_role') is-invalid @enderror" 
                                   value="{{ old('nom_role', $role->nom_role) }}" placeholder="Ex: Assistant médical" required>
                            @error('nom_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i> Description
                            </label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Décrivez les responsabilités de ce rôle...">{{ old('description', $role->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <strong class="d-block">Attention</strong>
                                <small>La modification de ce rôle n'affectera pas les permissions existantes des utilisateurs.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('gestionnaire.roles.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection