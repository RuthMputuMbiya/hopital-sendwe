@extends('layouts.medecin')

@section('title', 'Maladies')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-disease me-2" style="color: var(--secondary);"></i>
                Maladies
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-database me-1"></i> Gestion des maladies et pathologies
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.maladies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Nouvelle maladie
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-disease"></i></div>
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total maladies</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tags"></i></div>
                <div class="stat-number">{{ number_format($stats['categories']->count()) }}</div>
                <div class="stat-label">Catégories</div>
            </div>
        </div>
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i> Répartition par catégorie
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($stats['categories'] as $cat)
                        <div class="col-md-4 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $cat->categorie ?? 'Non catégorisé' }}</span>
                                <span class="badge bg-primary">{{ $cat->total }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maladies les plus fréquentes -->
    @if($stats['plus_frequentes']->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2" style="color: var(--secondary);"></i>
                    Maladies les plus fréquentes
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($stats['plus_frequentes'] as $maladie)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $maladie->maladie->nom_maladie ?? 'N/A' }}</strong>
                                        <div class="small text-muted">{{ $maladie->maladie->code_maladie ?? '' }}</div>
                                    </div>
                                    <span class="badge bg-danger">{{ $maladie->total }} consultations</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="card mb-4" data-aos="fade-up">
        <div class="card-body">
            <form method="GET" action="{{ route('medecin.maladies.index') }}" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par nom, code, symptômes..." 
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $categorie == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des maladies -->
    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des maladies
            <span class="badge bg-primary ms-2">{{ $maladies->total() }} maladies</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Maladie</th>
                            <th>Catégorie</th>
                            <th>Symptômes</th>
                            <th>Consultations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maladies as $maladie)
                        <tr>
                            <td><code>{{ $maladie->code_maladie }}</code></td>
                            <td>
                                <strong>{{ $maladie->nom_maladie }}</strong><br>
                                <small class="text-muted">{{ Str::limit($maladie->description, 60) }}</small>
                            </td>
                            <td>
                                @if($maladie->categorie)
                                    <span class="badge badge-info">{{ $maladie->categorie }}</span>
                                @else
                                    <span class="badge badge-secondary">Non catégorisée</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($maladie->symptomes, 50) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $maladie->consultations_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('medecin.maladies.show', $maladie->id_maladie) }}" 
                                       class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medecin.maladies.edit', $maladie->id_maladie) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(($maladie->consultations_count ?? 0) == 0)
                                    <form action="{{ route('medecin.maladies.destroy', $maladie->id_maladie) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Supprimer cette maladie ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-disease fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucune maladie trouvée</p>
                                <a href="{{ route('medecin.maladies.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-plus-circle me-2"></i> Ajouter une maladie
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $maladies->appends(['search' => $search, 'categorie' => $categorie])->links() }}
        </div>
    </div>
</div>
@endsection