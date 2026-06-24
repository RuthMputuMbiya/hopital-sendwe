@extends('layouts.receptionniste')

@section('title', 'Résultats d\'examens')

@section('page-title', 'Résultats d\'examens')
@section('page-subtitle', 'Consultez et imprimez les résultats pour les patients')

@section('content')
<div class="container-fluid">
    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par patient, numéro d'examen..." 
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="termine" {{ $statut == 'termine' ? 'selected' : '' }}>Résultats disponibles</option>
                        <option value="en_attente" {{ $statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="all" {{ $statut == 'all' ? 'selected' : '' }}>Tous les examens</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Résultats disponibles</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ $stats['aujourdhui'] }}</div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-number">{{ $stats['semaine'] }}</div>
                <div class="stat-label">Cette semaine</div>
            </div>
        </div>
    </div>

    <!-- Tableau des examens -->
    <div class="card">
        <div class="card-header bg-white">
            <i class="fas fa-list me-2 text-primary"></i> Liste des résultats d'examens
            <span class="badge bg-primary ms-2">{{ $examens->total() }} résultats</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Examen</th>
                            <th>Patient</th>
                            <th>Type d'examen</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examens as $examen)
                        <tr class="{{ $examen->statut == 'termine' ? 'table-success' : '' }}">
                            <td><code>{{ $examen->numero_examen }}</code></td>
                            <td>
                                <strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $examen->patient->numero_fiche ?? '' }}</small>
                            </td>
                            <td>{{ $examen->type_examen }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $examen->heure_examen }}</small>
                            </td>
                            <td>
                                @if($examen->statut == 'termine')
                                    <span class="badge badge-success"><i class="fas fa-check-circle me-1"></i> Disponible</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-hourglass-half me-1"></i> En attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if($examen->statut == 'termine')
                                        <a href="{{ route('receptionniste.examens.show', $examen->id_examen) }}" 
                                           class="btn btn-sm btn-primary" title="Voir résultat">
                                            <i class="fas fa-file-alt"></i> Résultat
                                        </a>
                                        <a href="{{ route('receptionniste.examens.print', $examen->id_examen) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Imprimer" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">En cours de traitement...</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
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
            {{ $examens->links() }}
        </div>
    </div>
</div>
@endsection