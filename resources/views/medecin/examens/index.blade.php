@extends('layouts.medecin')

@section('title', 'Résultats d\'examens')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-flask me-2" style="color: #1a4d6b;"></i>
                Résultats d'examens
            </h1>
            <p class="text-muted mb-0">Consultez les résultats des analyses de vos patients</p>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total examens</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['termines'] ?? 0) }}</div>
                <div class="stat-label">Examens terminés</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">{{ number_format($stats['en_attente'] ?? 0) }}</div>
                <div class="stat-label">En attente</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('medecin.examens.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher patient, examen..." 
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="termine" {{ ($statut ?? '') == 'termine' ? 'selected' : '' }}>Terminés</option>
                        <option value="en_attente" {{ ($statut ?? '') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="annule" {{ ($statut ?? '') == 'annule' ? 'selected' : '' }}>Annulés</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="{{ $dateDebut ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="{{ $dateFin ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des examens -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des examens
            <span class="badge bg-primary ms-2">{{ $examens->total() }} examens</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>N° Examen</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Laborantin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examens as $examen)
                        <tr>
                            <td><code>{{ $examen->numero_examen }}</code></td>
                            <td>
                                <strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $examen->patient->numero_fiche ?? '' }}</small>
                            </td>
                            <td>{{ $examen->type_examen }}</td>
                            <td>
                                {{ $examen->date_examen->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $examen->heure_examen }}</small>
                            </td>
                            <td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</td>
                            <td>
                                @if($examen->statut == 'termine')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Terminé</span>
                                @elseif($examen->statut == 'en_attente')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> En attente</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Annulé</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($examen->statut == 'termine')
                                        <a href="{{ route('medecin.examens.print', $examen->id_examen) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Imprimer résultat">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('medecin.examens.show', $examen->id_examen) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir détail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-flask fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun examen trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $examens->appends(['search' => $search ?? '', 'statut' => $statut ?? '', 'date_debut' => $dateDebut ?? '', 'date_fin' => $dateFin ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection