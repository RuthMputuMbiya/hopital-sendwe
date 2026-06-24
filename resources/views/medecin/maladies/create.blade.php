@extends('layouts.medecin')

@section('title', 'Ajouter une maladie')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-plus-circle me-2" style="color: var(--secondary);"></i>
                Ajouter une maladie
            </h1>
            <p class="text-muted mb-0">Enregistrer une nouvelle maladie dans la base de données</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.maladies.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto" data-aos="fade-up">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations de la maladie
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medecin.maladies.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-barcode me-1 text-primary"></i> Code maladie *
                                </label>
                                <input type="text" name="code_maladie" class="form-control @error('code_maladie') is-invalid @enderror" 
                                       value="{{ old('code_maladie') }}" placeholder="Ex: MLD001, CIM-10..." required>
                                @error('code_maladie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Code unique pour identifier la maladie</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1 text-primary"></i> Catégorie
                                </label>
                                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror">
                                    <option value="">Sélectionnez une catégorie</option>
                                    <option value="Maladies infectieuses" {{ old('categorie') == 'Maladies infectieuses' ? 'selected' : '' }}>Maladies infectieuses</option>
                                    <option value="Maladies cardiovasculaires" {{ old('categorie') == 'Maladies cardiovasculaires' ? 'selected' : '' }}>Maladies cardiovasculaires</option>
                                    <option value="Maladies respiratoires" {{ old('categorie') == 'Maladies respiratoires' ? 'selected' : '' }}>Maladies respiratoires</option>
                                    <option value="Maladies neurologiques" {{ old('categorie') == 'Maladies neurologiques' ? 'selected' : '' }}>Maladies neurologiques</option>
                                    <option value="Maladies métaboliques" {{ old('categorie') == 'Maladies métaboliques' ? 'selected' : '' }}>Maladies métaboliques</option>
                                    <option value="Cancers" {{ old('categorie') == 'Cancers' ? 'selected' : '' }}>Cancers</option>
                                    <option value="Maladies génétiques" {{ old('categorie') == 'Maladies génétiques' ? 'selected' : '' }}>Maladies génétiques</option>
                                    <option value="Autres" {{ old('categorie') == 'Autres' ? 'selected' : '' }}>Autres</option>
                                </select>
                                @error('categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-disease me-1 text-primary"></i> Nom de la maladie *
                            </label>
                            <input type="text" name="nom_maladie" class="form-control @error('nom_maladie') is-invalid @enderror" 
                                   value="{{ old('nom_maladie') }}" placeholder="Ex: Paludisme, Hypertension artérielle..." required>
                            @error('nom_maladie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1 text-primary"></i> Description *
                            </label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Description détaillée de la maladie..." required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-head-side-medical me-1 text-primary"></i> Symptômes
                            </label>
                            <textarea name="symptomes" rows="3" class="form-control @error('symptomes') is-invalid @enderror" 
                                      placeholder="Liste des symptômes associés...">{{ old('symptomes') }}</textarea>
                            @error('symptomes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Séparez les symptômes par des virgules ou des retours à la ligne</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> Cette maladie pourra être utilisée dans les consultations médicales.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection