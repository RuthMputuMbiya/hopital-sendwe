@extends('layouts.receptionniste')

@section('title', 'Mon historique')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history me-2 text-primary"></i> Mon historique</h2>
        <a href="{{ route('receptionniste.rapports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total enregistrements</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['aujourdhui']) }}</div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['semaine']) }}</div>
                <div class="stat-label">Cette semaine</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['mois']) }}</div>
                <div class="stat-label">Ce mois</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_fin" class="form-control" value="{{ $dateFin }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Numéro fiche</th>
                            <th>Téléphone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiques as $patient)
                        <tr>
                            <td>{{ $patient->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><strong>{{ $patient->nom_complet }}</strong></td>
                            <td>{{ $patient->numero_fiche }}</small></td>
                            <td>{{ $patient->telephone }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4">Aucun enregistrement</small></td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $historiques->links() }}
        </div>
    </div>
</div>
@endsection