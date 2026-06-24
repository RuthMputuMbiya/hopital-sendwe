@extends('layouts.laborantin')

@section('title', 'Bons d\'analyse reçus')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-medical me-2" style="color: #1a4d6b;"></i>
                Bons d'analyse reçus
            </h1>
            <p class="text-muted mb-0">Gestion des demandes d'analyse du laboratoire</p>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total bons</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ number_format($stats['en_attente'] ?? 0) }}</div>
                <div class="stat-label">En attente</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="stat-number">{{ number_format($stats['en_cours'] ?? 0) }}</div>
                <div class="stat-label">En cours</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['termines'] ?? 0) }}</div>
                <div class="stat-label">Terminés</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ ($statut ?? '') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_cours" {{ ($statut ?? '') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="termine" {{ ($statut ?? '') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Bons d'analyse
            <span class="badge bg-primary ms-2">{{ $bons->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>N° Bon</th>
                            <th>Patient</th>
                            <th>Médecin</th>
                            <th>Date</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bons as $bon)
                        <tr>
                            <td><code>{{ $bon->numero_bon }}</code></td>
                            <td>
                                <strong>{{ $bon->patient->nom_complet ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $bon->patient->numero_fiche ?? '' }}</small>
                            </td>
                            <td>Dr. {{ $bon->medecin->nom_complet ?? 'N/A' }}</td>
                            <td>
                                {{ $bon->date_demande->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $bon->heure_demande }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : ($bon->priorite == 'moyen' ? 'warning' : 'primary') }}">
                                    {{ $bon->priorite == 'urgent' ? 'Urgent' : ($bon->priorite == 'moyen' ? 'Moyen' : 'Normal') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bon->statut == 'termine' ? 'success' : ($bon->statut == 'en_cours' ? 'warning' : 'secondary') }}">
                                    {{ $bon->statut == 'termine' ? 'Terminé' : ($bon->statut == 'en_cours' ? 'En cours' : 'En attente') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('laborantin.bons-analyse.show', $bon->id_bon) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($bon->statut == 'en_attente')
                                        <a href="{{ route('laborantin.bons-analyse.prelever-form', $bon->id_bon) }}" 
                                           class="btn btn-sm btn-warning" title="Prélever">
                                            <i class="fas fa-syringe"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('laborantin.bons-analyse.print', $bon->id_bon) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Imprimer" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun bon d'analyse reçu</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $bons->appends(['search' => $search ?? '', 'statut' => $statut ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection