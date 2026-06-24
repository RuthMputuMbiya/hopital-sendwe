@extends('layouts.medecin')

@section('title', 'Mes patients')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-users me-2" style="color: #1a4d6b;"></i>
                Mes patients
            </h1>
            <p class="text-muted mb-0">Gestion des dossiers patients</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.consultations.create') }}" class="btn btn-primary">
                <i class="fas fa-stethoscope me-2"></i> Nouvelle consultation
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total patients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['consultes'] ?? 0) }}</div>
                <div class="stat-label">Patients consultés</div>
                <div class="stat-trend up"><i class="fas fa-stethoscope me-1"></i> Déjà vus</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ number_format($stats['non_consultes'] ?? 0) }}</div>
                <div class="stat-label">Non consultés</div>
                <div class="stat-trend down"><i class="fas fa-clock me-1"></i> À voir</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number">{{ number_format($stats['mois_actuel'] ?? 0) }}</div>
                <div class="stat-label">Nouveaux ce mois</div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('medecin.patients.index') }}" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par nom, prénom, matricule ou téléphone..." 
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="">Tous les patients</option>
                        <option value="consultes" {{ ($statut ?? '') == 'consultes' ? 'selected' : '' }}>Patients consultés</option>
                        <option value="non_consultes" {{ ($statut ?? '') == 'non_consultes' ? 'selected' : '' }}>Patients non consultés</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des patients -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des patients
            <span class="badge bg-primary ms-2">{{ $patients->total() }} patients</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>N° Fiche</th>
                            <th>Nom complet</th>
                            <th>Sexe</th>
                            <th>Âge</th>
                            <th>Téléphone</th>
                            <th>Dernière visite</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        @php
                            // ✅ CORRECTION : La relation utilise maintenant 'id_patient'
                            $derniereConsultation = $patient->consultations()->latest('date_consultation')->first();
                        @endphp
                        <tr>
                            <td><code>{{ $patient->numero_fiche }}</code></td>
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
                            <td>{{ $patient->age ?? '-' }} ans</td>
                            <td>{{ $patient->telephone }}</td>
                            <td>
                                @if($derniereConsultation)
                                    {{ \Carbon\Carbon::parse($derniereConsultation->date_consultation)->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($derniereConsultation->date_consultation)->diffForHumans() }}</small>
                                @else
                                    <span class="badge bg-warning">Jamais consulté</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('medecin.consultations.create', ['patientId' => $patient->id_patient]) }}" 
                                       class="btn btn-sm btn-outline-success" title="Nouvelle consultation">
                                        <i class="fas fa-stethoscope"></i>
                                    </a>
                                    <a href="{{ route('medecin.bons-analyse.create', $patient->id_patient) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Bon d'analyse">
                                        <i class="fas fa-flask"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-notes-medical fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun patient trouvé</p>
                                <a href="{{ route('medecin.consultations.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-plus-circle me-2"></i> Commencer une consultation
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $patients->appends(['search' => $search ?? '', 'statut' => $statut ?? ''])->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        background: #e3f2fd;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .stat-icon i {
        font-size: 24px;
        color: #1a4d6b;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #6c757d;
        font-size: 13px;
        font-weight: 500;
    }
    .stat-trend {
        font-size: 11px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e9ecef;
    }
    .stat-trend.up {
        color: #28a745;
    }
    .stat-trend.down {
        color: #dc3545;
    }
    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 11px;
    }
    .btn-group .btn {
        border-radius: 8px;
        margin: 0 2px;
    }
</style>
@endpush
@endsection