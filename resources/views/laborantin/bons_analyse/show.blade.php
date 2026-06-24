@extends('layouts.laborantin')

@section('title', 'Bon d\'analyse N°' . $bon->numero_bon)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-medical me-2" style="color: #1a4d6b;"></i>
                Bon d'analyse <span class="text-muted">N° {{ $bon->numero_bon }}</span>
            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $bon->statut == 'termine' ? 'success' : ($bon->statut == 'en_cours' ? 'warning' : 'secondary') }}">
                    {{ $bon->statut == 'termine' ? 'Terminé' : ($bon->statut == 'en_cours' ? 'En cours' : 'En attente') }}
                </span>
                <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : ($bon->priorite == 'moyen' ? 'warning' : 'primary') }}">
                    {{ $bon->priorite == 'urgent' ? 'Urgent' : ($bon->priorite == 'moyen' ? 'Moyen' : 'Normal') }}
                </span>
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.bons-analyse.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('laborantin.bons-analyse.print', $bon->id_bon) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i> Imprimer
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Infos Patient -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Informations générales du patient
                </div>
                <div class="card-body">
                    <p><strong>Nom, Post-nom et Prénom :</strong> {{ $bon->patient->nom_complet ?? 'N/A' }}</p>
                    <p><strong>Sexe :</strong> {{ $bon->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                    <p><strong>Date de naissance :</strong> {{ isset($bon->patient->date_naissance) ? $bon->patient->date_naissance->format('d/m/Y') : 'N/A' }}</p>
                    <p><strong>Téléphone :</strong> {{ $bon->patient->telephone ?? 'N/A' }}</p>
                    @if($bon->hospitalise)
                        <p><strong>Hospitalisé :</strong> Oui - Pavillon {{ $bon->pavillon }}, Chambre {{ $bon->chambre }}, Lit {{ $bon->lit }}</p>
                    @else
                        <p><strong>Hospitalisé :</strong> Non</p>
                        <p><strong>Adresse :</strong> {{ $bon->adresse_domicile ?? $bon->patient->adresse ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Infos Médecin -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-md me-2"></i> Médecin/Clinicién demandeur
                </div>
                <div class="card-body">
                    <p><strong>Nom et prénom :</strong> Dr. {{ $bon->prenom_medecin_demandeur }} {{ $bon->nom_medecin_demandeur }}</p>
                    <p><strong>Date :</strong> {{ $bon->date_demande->format('d/m/Y') }}</p>
                    <p><strong>Heure :</strong> {{ $bon->heure_demande }}</p>
                    <p><strong>Service :</strong> {{ $bon->service_demandeur }}</p>
                    <p><strong>Téléphone :</strong> {{ $bon->telephone_demandeur }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Préleveur -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-syringe me-2"></i> Préleveur
                </div>
                <div class="card-body">
                    @if($bon->statut == 'en_attente')
                        <form method="POST" action="{{ route('laborantin.bons-analyse.prelever', $bon->id_bon) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom_preleveur" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="prenom_preleveur" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date_prelevement" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Heure</label>
                                    <input type="time" name="heure_prelevement" class="form-control" value="{{ date('H:i') }}" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">N° Id. Client</label>
                                    <input type="text" name="id_client_labo" class="form-control" placeholder="N° client">
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-check me-2"></i> Enregistrer le prélèvement
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <p><strong>Nom :</strong> {{ $bon->nom_preleveur ?? 'N/A' }}</p>
                        <p><strong>Prénom :</strong> {{ $bon->prenom_preleveur ?? 'N/A' }}</p>
                        <p><strong>Date :</strong> {{ $bon->date_prelevement ? Carbon\Carbon::parse($bon->date_prelevement)->format('d/m/Y') : '-' }}</p>
                        <p><strong>Heure :</strong> {{ $bon->heure_prelevement ?? '-' }}</p>
                        <p><strong>N° Id. Client :</strong> {{ $bon->id_client_labo ?? '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Type d'échantillon -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-vial me-2"></i> Type d'échantillon
                </div>
                <div class="card-body">
                    <p><strong>Type :</strong> 
                        @if($bon->type_echantillon == 'sang') Sang
                        @elseif($bon->type_echantillon == 'selles') Selles
                        @elseif($bon->type_echantillon == 'urines') Urines
                        @elseif($bon->type_echantillon == 'lcr') LCR
                        @elseif($bon->type_echantillon == 'crachat') Crachat
                        @elseif($bon->type_echantillon == 'autres') Autres
                        @else Non spécifié
                        @endif
                    </p>
                    @if($bon->autres_echantillon)
                        <p><strong>Précision :</strong> {{ $bon->autres_echantillon }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Examens et Résultats -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-microscope me-2"></i> Examens et Résultats
        </div>
        <div class="card-body">
            @if($bon->statut == 'en_cours' || $bon->statut == 'termine')
                <form method="POST" action="{{ route('laborantin.bons-analyse.resultats', $bon->id_bon) }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Examen</th>
                                    <th>Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $categories = [
                                        'parasitologie' => 'Parasitologie',
                                        'hematologie' => 'Hématologie',
                                        'microbiologie' => 'Microbiologie',
                                        'biochimie' => 'Biochimie',
                                        'immuno_serologie' => 'Immuno-sérologie'
                                    ];
                                @endphp
                                @if($bon->examens)
                                    @foreach($categories as $catKey => $catLabel)
                                        @if(isset($bon->examens[$catKey]))
                                            @foreach($bon->examens[$catKey] as $examenKey => $valeur)
                                                @if($valeur == 1)
                                                    <tr>
                                                        <td>{{ $catLabel }}</td>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $examenKey)) }}</td>
                                                        <td>
                                                            <input type="text" name="resultats[{{ $catKey }}][{{ $examenKey }}]" 
                                                                   class="form-control" 
                                                                   placeholder="Résultat..." 
                                                                   value="{{ $bon->resultats[$catKey][$examenKey] ?? '' }}"
                                                                   {{ $bon->statut == 'termine' ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif

                                <!-- Autres examens -->
                                @if(isset($bon->examens['autres']))
                                    @foreach($bon->examens['autres'] as $index => $valeur)
                                        @if(!empty($valeur))
                                            <tr>
                                                <td>Autres</td>
                                                <td>{{ $valeur }}</td>
                                                <td>
                                                    <input type="text" name="resultats[autres][{{ $index }}]" 
                                                           class="form-control" 
                                                           placeholder="Résultat..."
                                                           value="{{ $bon->resultats['autres'][$index] ?? '' }}"
                                                           {{ $bon->statut == 'termine' ? 'readonly' : '' }}>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Observations du laborantin</label>
                            <textarea name="observations_laborantin" class="form-control" rows="3" 
                                      {{ $bon->statut == 'termine' ? 'readonly' : '' }}>{{ $bon->observations_laborantin }}</textarea>
                        </div>
                    </div>

                    @if($bon->statut != 'termine')
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-save me-2"></i> Enregistrer les résultats
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('laborantin.bons-analyse.retourner', $bon->id_bon) }}" 
                                   class="btn btn-primary w-100"
                                   onclick="return confirm('Confirmer le retour du bon au médecin ?')">
                                    <i class="fas fa-paper-plane me-2"></i> Retourner au médecin
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i> 
                            Résultats enregistrés et retournés au médecin le {{ $bon->date_retour ? Carbon\Carbon::parse($bon->date_retour)->format('d/m/Y H:i') : '-' }}
                        </div>
                        <div class="text-end">
                            <a href="{{ route('laborantin.bons-analyse.print', $bon->id_bon) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-print me-2"></i> Imprimer le bon
                            </a>
                        </div>
                    @endif
                </form>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> 
                    En attente du prélèvement. Une fois le prélèvement effectué, vous pourrez saisir les résultats.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection