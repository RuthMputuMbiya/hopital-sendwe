@extends('layouts.laborantin')

@section('title', 'Liste des examens')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1"><i class="fas fa-flask me-2" style="color: var(--secondary);"></i>Liste des examens</h1>
            <p class="text-muted mb-0">Gestion des examens médicaux</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.examens.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Nouvel examen</a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-flask"></i></div><div class="stat-number">{{ number_format($stats['total']) }}</div><div class="stat-label">Total examens</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-number">{{ number_format($stats['attente']) }}</div><div class="stat-label">En attente</div><div class="stat-trend warning">À traiter</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-number">{{ number_format($stats['termines']) }}</div><div class="stat-label">Terminés</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-ban"></i></div><div class="stat-number">{{ number_format($stats['annules']) }}</div><div class="stat-label">Annulés</div></div></div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ $search }}"></div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ $statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="termine" {{ $statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="annule" {{ $statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-4"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Filtrer</button></div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card">
        <div class="card-header"><i class="fas fa-list me-2"></i>Examens <span class="badge bg-primary ms-2">{{ $examens->total() }}</span></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>N° Examen</th><th>Patient</th><th>Type</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($examens as $examen)
                        <tr>
                            <td><code>{{ $examen->numero_examen }}</code></td>
                            <td><strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong><br><small>{{ $examen->patient->numero_fiche ?? '' }}</small></td>
                            <td>{{ $examen->type_examen }}</td>
                            <td>{{ $examen->date_examen->format('d/m/Y') }}<br><small>{{ $examen->heure_examen }}</small></td>
                            <td>
                                @if($examen->statut == 'en_attente')<span class="badge badge-warning">En attente</span>
                                @elseif($examen->statut == 'termine')<span class="badge badge-success">Terminé</span>
                                @else<span class="badge badge-danger">Annulé</span>@endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('laborantin.examens.show', $examen->id_examen) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                    @if($examen->statut == 'en_attente')
                                    <a href="{{ route('laborantin.examens.resultat', $examen->id_examen) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    @endif
                                </div>
                             </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">Aucun examen trouvé</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $examens->links() }}</div>
    </div>
</div>
@endsection