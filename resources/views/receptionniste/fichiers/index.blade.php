@extends('layouts.receptionniste')

@section('title', 'Documents du patient')

@section('page-title', 'Documents médicaux')
@section('page-subtitle', $patient->nom_complet . ' - ' . $patient->numero_fiche)

@section('content')
<div class="container-fluid">
    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Total documents</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-image"></i></div>
                <div class="stat-number">{{ $stats['images'] }}</div>
                <div class="stat-label">Images</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-pdf"></i></div>
                <div class="stat-number">{{ $stats['pdfs'] }}</div>
                <div class="stat-label">PDFs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-word"></i></div>
                <div class="stat-number">{{ $stats['documents'] }}</div>
                <div class="stat-label">Documents</div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mb-4">
        <div class="btn-group">
            <a href="{{ route('receptionniste.fichiers.create', $patient->id_patient) }}" class="btn btn-primary">
                <i class="fas fa-upload me-2"></i> Ajouter un document
            </a>
            <a href="{{ route('receptionniste.patients.show', $patient->id_patient) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour au dossier
            </a>
        </div>
    </div>

    <!-- Liste des fichiers -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-folder-open me-2"></i> Documents du patient
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Nom du fichier</th>
                            <th>Taille</th>
                            <th>Ajouté par</th>
                            <th>Date d'ajout</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichiers as $fichier)
                        <tr>
                            <td>
                                <div class="text-center">
                                    @if($fichier->type_fichier == 'image')
                                        <i class="fas fa-image fa-2x text-primary"></i>
                                    @elseif($fichier->type_fichier == 'pdf')
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    @elseif($fichier->type_fichier == 'document')
                                        <i class="fas fa-file-word fa-2x text-info"></i>
                                    @elseif($fichier->type_fichier == 'radiologie')
                                        <i class="fas fa-x-ray fa-2x text-secondary"></i>
                                    @else
                                        <i class="fas fa-file fa-2x text-muted"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong>{{ $fichier->titre }}</strong><br>
                                <small class="text-muted">{{ Str::limit($fichier->description, 60) }}</small>
                            </td>
                            <td>
                                {{ $fichier->nom_fichier }}<br>
                                <small class="text-muted">.{{ $fichier->extension }}</small>
                            </td>
                            <td>{{ $fichier->taille_formattee }}</small></td>
                            <td>{{ $fichier->uploader->nom_complet ?? auth()->user()->nom_complet }}<br>
                                <small class="text-muted">{{ $fichier->uploader->role->nom_role ?? '' }}</small>
                            </td>
                            <td>
                                {{ $fichier->created_at->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $fichier->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('receptionniste.fichiers.show', $fichier->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="Aperçu">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('receptionniste.fichiers.download', $fichier->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('receptionniste.fichiers.destroy', $fichier->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement ce document ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun document dans le dossier de ce patient</p>
                                <a href="{{ route('receptionniste.fichiers.create', $patient->id_patient) }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-upload me-2"></i> Ajouter un document
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $fichiers->links() }}
        </div>
    </div>
</div>
@endsection