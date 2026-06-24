@extends('layouts.gestionnaire')

@section('title', 'Créer un rôle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-plus-circle me-2" style="color: var(--secondary);"></i>
                Créer un nouveau rôle
            </h1>
            <p class="text-muted mb-0">Définissez un nouveau rôle pour les utilisateurs</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('gestionnaire.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations du rôle
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('gestionnaire.roles.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-tag me-1 text-primary"></i> Nom du rôle *
                            </label>
                            <input type="text" name="nom_role" class="form-control @error('nom_role') is-invalid @enderror" 
                                   value="{{ old('nom_role') }}" placeholder="Ex: Assistant médical, Secrétaire médicale..." required>
                            @error('nom_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Nom unique qui décrit le rôle</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1 text-primary"></i> Description
                            </label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Décrivez les responsabilités et permissions de ce rôle...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Description détaillée des responsabilités (optionnel)</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information :</strong> Les permissions spécifiques pour ce rôle devront être configurées dans le code.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Créer le rôle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection