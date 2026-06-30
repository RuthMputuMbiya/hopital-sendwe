@extends('layouts.receptionniste')

@section('title', 'Liste des patients')

@section('page-title', 'Liste des patients')
@section('page-subtitle', 'Gestion des dossiers patients')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-notes-medical me-2" style="color: var(--secondary);"></i>
                Liste des patients
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-users me-1"></i> Gestion des dossiers patients
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('receptionniste.patients.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i> Nouveau patient
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total patients</div>
                <div class="stat-trend up"><i class="fas fa-chart-line me-1"></i> Enregistrés</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ number_format($stats['aujourdhui']) }}</div>
                <div class="stat-label">Aujourd'hui</div>
                <div class="stat-trend up"><i class="fas fa-user-plus me-1"></i> Nouveaux</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-mars"></i></div>
                <div class="stat-number">{{ number_format($stats['hommes']) }}</div>
                <div class="stat-label">Hommes</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-venus"></i></div>
                <div class="stat-number">{{ number_format($stats['femmes']) }}</div>
                <div class="stat-label">Femmes</div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="card mb-4" data-aos="fade-up">
        <div class="card-header bg-white">
            <i class="fas fa-search me-2 text-primary"></i> Recherche avancée
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('receptionniste.patients.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par nom, prénom, matricule, téléphone, numéro fiche..." 
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des patients -->
    <div class="card" data-aos="fade-up">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2 text-primary"></i> Liste des patients
                    <span class="badge bg-primary ms-2">{{ $patients->total() }} patients</span>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-print me-1"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Fiche</th>
                            <th>Nom complet</th>
                            <th>Sexe</th>
                            <th>Âge</th>
                            <th>Date naissance</th>
                            <th>Téléphone</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td>
                                <code>{{ $patient->numero_fiche }}</code>
                            </td>
                            <td>
                                <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong><br>
                                <small class="text-muted">{{ $patient->postnom }}</small>
                            </td>
                            <td>
                                @if($patient->sexe === 'M')
                                    <span class="badge bg-info"><i class="fas fa-mars"></i> Masculin</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-venus"></i> Féminin</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->age)
                                    <span class="fw-bold text-primary">{{ $patient->age }} ans</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</td>
                            <td>
                                <i class="fas fa-phone me-1 text-muted"></i> {{ $patient->telephone }}
                                @if($patient->telephone_secondaire)
                                    <br><small class="text-muted">{{ $patient->telephone_secondaire }}</small>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($patient->created_at)->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($patient->created_at)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('receptionniste.patients.show', $patient->id_patient) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir dossier">
                                        <i class="fas fa-folder-open"></i>
                                    </a>
                                    <a href="{{ route('receptionniste.patients.edit', $patient->id_patient) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($patient->consultations()->count() == 0)
                                    <form action="{{ route('receptionniste.patients.destroy', $patient->id_patient) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('⚠️ Supprimer définitivement ce patient ?\n\nCette action est irréversible.')">
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
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-notes-medical fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun patient trouvé</p>
                                <a href="{{ route('receptionniste.patients.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-user-plus me-2"></i> Ajouter un patient
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Affichage de {{ $patients->firstItem() ?? 0 }} à {{ $patients->lastItem() ?? 0 }} sur {{ $patients->total() }} patients
                    </small>
                </div>
                <div>
                    {{ $patients->appends(['search' => $search ?? ''])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-left: 4px solid #1a4d6b;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .stat-icon {
        font-size: 28px;
        color: #1a4d6b;
        opacity: 0.3;
        position: absolute;
        right: 15px;
        top: 15px;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1a4d6b;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }
    .stat-trend {
        font-size: 12px;
        font-weight: 600;
        margin-top: 5px;
    }
    .stat-trend.up {
        color: #28a745;
    }
    .badge-info {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .badge-danger {
        background-color: #fee7e7;
        color: #dc3545;
    }
    .table tbody tr {
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #eef2ff;
    }
</style>
@endpush