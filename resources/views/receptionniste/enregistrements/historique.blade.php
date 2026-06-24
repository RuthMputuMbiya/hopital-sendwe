@extends('layouts.receptionniste')

@section('title', 'Historique des enregistrements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-history me-2" style="color: var(--secondary);"></i>
                Historique des enregistrements
            </h1>
            <p class="text-muted mb-0">Traçabilité complète des activités</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('receptionniste.enregistrements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    <!-- Cartes stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-database"></i></div>
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total entrées</div></div>
        </div>
        <div class="col-md-3">
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-number">{{ number_format($stats['aujourdhui']) }}</div>
            <div class="stat-label">Aujourd'hui</div></div>
        </div>
        <div class="col-md-3">
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
            <div class="stat-number">{{ number_format($stats['patients']) }}</div>
            <div class="stat-label">Patients</div></div>
        </div>
        <div class="col-md-3">
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-stethoscope"></i></div>
            <div class="stat-number">{{ number_format($stats['consultations']) }}</div>
            <div class="stat-label">Consultations</div></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" value="{{ $dateFin }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="patients" {{ $type == 'patients' ? 'selected' : '' }}>Patients</option>
                        <option value="consultations" {{ $type == 'consultations' ? 'selected' : '' }}>Consultations</option>
                        <option value="examens" {{ $type == 'examens' ? 'selected' : '' }}>Examens</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau historique -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Historique détaillé
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Patient</th>
                            <th>Utilisateur</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiques as $histo)
                        <tr>
                            <td>{{ $histo->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-info">{{ $histo->action }}</span></td>
                            <td>{{ $histo->table_concernee }}</td>
                            <td>{{ $histo->patient->nom_complet ?? 'N/A' }}</td>
                            <td>{{ $histo->user->nom_complet ?? 'N/A' }}</td>
                            <td>{{ Str::limit($histo->description, 60) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">Aucun historique</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $historiques->links() }}</div>
    </div>
</div>
@endsection