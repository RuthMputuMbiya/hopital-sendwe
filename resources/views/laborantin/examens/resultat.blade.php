@extends('layouts.laborantin')

@section('title', 'Saisir résultat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div><h1 class="h2"><i class="fas fa-edit me-2" style="color: var(--secondary);"></i>Saisir le résultat</h1><p class="text-muted">Examen: {{ $examen->numero_examen }} - {{ $examen->type_examen }}</p></div>
        <div><a href="{{ route('laborantin.examens.show', $examen->id_examen) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a></div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Résultats d'analyse</div>
                <div class="card-body">
                    <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Patient: <strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong> - Type: {{ $examen->type_examen }}</div>

                    <form method="POST" action="{{ route('laborantin.examens.update-resultat', $examen->id_examen) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-chart-line me-1 text-primary"></i> Résultat *</label>
                            <textarea name="resultat" class="form-control @error('resultat') is-invalid @enderror" rows="8" required>{{ old('resultat', $examen->resultat) }}</textarea>
                            @error('resultat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Format libre - Décrivez les résultats en détail</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-check-circle me-1 text-primary"></i> Statut *</label>
                                <select name="statut" class="form-select" required>
                                    <option value="termine" {{ old('statut', $examen->statut) == 'termine' ? 'selected' : '' }}>Terminé - Résultat final</option>
                                    <option value="en_attente" {{ old('statut', $examen->statut) == 'en_attente' ? 'selected' : '' }}>En attente - Résultat temporaire</option>
                                    <option value="annule" {{ old('statut', $examen->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer le résultat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection