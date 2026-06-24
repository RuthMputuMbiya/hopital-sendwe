@extends('layouts.receptionniste')

@section('title', 'Aperçu du document')

@section('page-title', 'Aperçu du document')
@section('page-subtitle', $fichier->titre)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="{{ $fichier->icone }} me-2"></i> {{ $fichier->titre }}
                        </h5>
                        <div class="btn-group">
                            <a href="{{ route('receptionniste.fichiers.download', $fichier->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Télécharger
                            </a>
                            <a href="{{ route('receptionniste.fichiers.index', $fichier->id_patient) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Informations du document -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small">Patient</label>
                                <p class="fw-semibold">{{ $fichier->patient->nom_complet }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small">Numéro fiche</label>
                                <p class="fw-semibold">{{ $fichier->patient->numero_fiche }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="text-muted small">Type de document</label>
                                <p>
                                    @if($fichier->type_fichier == 'image')
                                        <span class="badge bg-primary">Image</span>
                                    @elseif($fichier->type_fichier == 'pdf')
                                        <span class="badge bg-danger">PDF</span>
                                    @elseif($fichier->type_fichier == 'document')
                                        <span class="badge bg-info">Document</span>
                                    @else
                                        <span class="badge bg-secondary">Radiologie</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="text-muted small">Taille du fichier</label>
                                <p>{{ $fichier->taille_formattee }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <label class="text-muted small">Ajouté par</label>
                                <p>{{ $fichier->uploader->nom_complet ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($fichier->description)
                        <div class="col-12">
                            <div class="info-group">
                                <label class="text-muted small">Description</label>
                                <p>{{ $fichier->description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Aperçu du fichier -->
                    <div class="preview-container border rounded-4 p-4 bg-light">
                        @if($fichier->type_fichier == 'image')
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $fichier->chemin_fichier) }}" alt="{{ $fichier->titre }}" class="img-fluid rounded" style="max-height: 500px;">
                            </div>
                        @elseif($fichier->type_fichier == 'pdf')
                            <div class="text-center">
                                <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                                <p class="mb-2"><strong>{{ $fichier->nom_fichier }}</strong></p>
                                <p class="text-muted small">{{ $fichier->taille_formattee }}</p>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Le PDF peut être visualisé après téléchargement.
                                </div>
                                <a href="{{ route('receptionniste.fichiers.download', $fichier->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i> Télécharger le PDF
                                </a>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="{{ $fichier->icone }} fa-5x text-primary mb-3"></i>
                                <p class="mb-2"><strong>{{ $fichier->nom_fichier }}</strong></p>
                                <p class="text-muted small">{{ $fichier->taille_formattee }}</p>
                                <a href="{{ route('receptionniste.fichiers.download', $fichier->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i> Télécharger le document
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i> Ajouté le {{ $fichier->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection