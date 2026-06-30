@extends('layouts.laborantin')

@section('title', 'Saisie des résultats - Bon N°' . $bon->numero_bon)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-flask me-2" style="color: #1a4d6b;"></i>
                Saisie des résultats
            </h1>
            <p class="text-muted mb-0">
                Bon d'analyse N° <strong>{{ $bon->numero_bon }}</strong>
                <span class="badge bg-warning ms-2">Patient: {{ $bon->patient->nom_complet ?? 'N/A' }}</span>
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit me-2"></i> Saisie des résultats d'analyse
                    <span class="badge bg-info ms-2">{{ count($examensListe) }} examens à renseigner</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('laborantin.bons-analyse.resultats', $bon->id_bon) }}">
                        @csrf
                        
                        @if(count($examensListe) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Catégorie</th>
                                        <th>Examen</th>
                                        <th>Unité</th>
                                        <th>Valeurs normales</th>
                                        <th>Résultat</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examensListe as $index => $examen)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $examen['categorie'])) }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $examen['label'] }}</strong></td>
                                        <td>{{ $examen['unite'] }}</td>
                                        <td><small class="text-muted">{{ $examen['valeurs_normales'] }}</small></td>
                                        <td>
                                            <input type="text" name="resultats[{{ $index }}][valeur]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Résultat..."
                                                   value="{{ old('resultats.'.$index.'.valeur') }}">
                                        </td>
                                        <td>
                                            <input type="text" name="resultats[{{ $index }}][commentaire]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Commentaire..."
                                                   value="{{ old('resultats.'.$index.'.commentaire') }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Aucun examen à renseigner pour ce bon d'analyse.
                        </div>
                        @endif

                        <!-- Observations -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Observations du laborantin</label>
                                <textarea name="observations_laborantin" class="form-control" rows="3" 
                                          placeholder="Observations complémentaires sur les analyses...">{{ old('observations_laborantin') }}</textarea>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('laborantin.bons-analyse.show', $bon->id_bon) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i> Enregistrer les résultats
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ⭐ BILAN INFIRMIER - AFFICHÉ EN BAS ⭐ -->
    @if($bon->poids_infirmier || $bon->taille_infirmier || $bon->temperature_infirmier || $bon->infirmier)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-user-nurse me-2"></i> 
                    Bilan initial - Infirmier
                    @if($bon->infirmier)
                    <span class="badge bg-light text-dark ms-2">
                        {{ $bon->infirmier->nom_complet ?? 'N/A' }}
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($bon->poids_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">Poids</label>
                                <p class="fw-bold">{{ $bon->poids_infirmier }} kg</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->taille_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">Taille</label>
                                <p class="fw-bold">{{ $bon->taille_infirmier }} cm</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->temperature_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">Température</label>
                                <p class="fw-bold">{{ $bon->temperature_infirmier }} °C</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->tension_arterielle_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">Tension</label>
                                <p class="fw-bold">{{ $bon->tension_arterielle_infirmier }}</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->frequence_cardiaque_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">FC</label>
                                <p class="fw-bold">{{ $bon->frequence_cardiaque_infirmier }} bpm</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->frequence_respiratoire_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">FR</label>
                                <p class="fw-bold">{{ $bon->frequence_respiratoire_infirmier }}/min</p>
                            </div>
                        </div>
                        @endif
                        @if($bon->spo2_infirmier)
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="text-muted small">SpO2</label>
                                <p class="fw-bold">{{ $bon->spo2_infirmier }}%</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($bon->motif_infirmier || $bon->symptomes_infirmier || $bon->observations_infirmier)
                    <hr>
                    <div class="row">
                        @if($bon->motif_infirmier)
                        <div class="col-md-4">
                            <label class="text-muted small">Motif</label>
                            <p class="fw-bold">{{ $bon->motif_infirmier }}</p>
                        </div>
                        @endif
                        @if($bon->symptomes_infirmier)
                        <div class="col-md-4">
                            <label class="text-muted small">Symptômes</label>
                            <p class="fw-bold">{{ $bon->symptomes_infirmier }}</p>
                        </div>
                        @endif
                        @if($bon->observations_infirmier)
                        <div class="col-md-4">
                            <label class="text-muted small">Observations</label>
                            <p class="fw-bold">{{ $bon->observations_infirmier }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($bon->allergies_infirmier || $bon->traitements_infirmier)
                    <hr>
                    <div class="row">
                        @if($bon->allergies_infirmier)
                        <div class="col-md-6">
                            <label class="text-muted small text-danger">Allergies</label>
                            <p class="fw-bold text-danger">{{ $bon->allergies_infirmier }}</p>
                        </div>
                        @endif
                        @if($bon->traitements_infirmier)
                        <div class="col-md-6">
                            <label class="text-muted small">Traitements</label>
                            <p class="fw-bold">{{ $bon->traitements_infirmier }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .info-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 5px;
    }
    .info-item label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 14px;
    }
    .table th {
        font-size: 12px;
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .form-control-sm {
        font-size: 13px;
        padding: 4px 8px;
    }
</style>
@endpush
@endsection