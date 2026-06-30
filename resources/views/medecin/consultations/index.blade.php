@extends('layouts.medecin')

@section('title', 'Mes consultations')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-stethoscope me-2" style="color: #1a4d6b;"></i>
                Mes consultations
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('medecin.consultations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Nouvelle consultation
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
                <div class="stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total consultations</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number">{{ number_format($stats['aujourdhui'] ?? 0) }}</div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ number_format($stats['en_attente_infirmier'] ?? 0) }}</div>
                <div class="stat-label">En attente infirmier</div>
                <div class="stat-trend warning">À traiter</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['bilan_fait'] ?? 0) }}</div>
                <div class="stat-label">Bilan fait</div>
                <div class="stat-trend up">En attente médecin</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4" data-aos="fade-up">
        <div class="card-body">
            <form method="GET" action="{{ route('medecin.consultations.index') }}" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher patient, diagnostic..." 
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente_infirmier" {{ ($statut ?? '') == 'en_attente_infirmier' ? 'selected' : '' }}>En attente infirmier</option>
                        <option value="bilan_fait" {{ ($statut ?? '') == 'bilan_fait' ? 'selected' : '' }}>Bilan fait</option>
                        <option value="en_consultation" {{ ($statut ?? '') == 'en_consultation' ? 'selected' : '' }}>En consultation</option>
                        <option value="termine" {{ ($statut ?? '') == 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="annule" {{ ($statut ?? '') == 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="{{ $dateDebut ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="{{ $dateFin ?? '' }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des consultations -->
    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des consultations
            <span class="badge bg-primary ms-2">{{ $consultations->total() }} consultations</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>N° Consultation</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Infirmier</th>
                            <th>Diagnostic</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $consultation)
                        <tr>
                            <td><code>{{ $consultation->numero_consultation ?? 'N/A' }}</code></td>
                            <td>
                                {{ isset($consultation->date_consultation) ? $consultation->date_consultation->format('d/m/Y') : 'N/A' }}<br>
                                <small class="text-muted">{{ $consultation->heure_consultation ?? '' }}</small>
                            </td>
                            <td>
                                <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $consultation->patient->numero_fiche ?? '' }}</small>
                            </td>
                            <td>
                                @if($consultation->infirmier)
                                    <span class="badge bg-info">
                                        <i class="fas fa-user-nurse me-1"></i>
                                        {{ $consultation->infirmier->nom_complet ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($consultation->diagnostic ?? 'N/A', 40) }}</td>
                            <td>
                                {!! $consultation->statut_consultation_badge !!}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($consultation->statut_consultation != 'termine')
                                    <a href="{{ route('medecin.consultations.edit', $consultation->id_consultation) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('medecin.bons-analyse.create', ['patientId' => $consultation->id_patient]) }}" 
                                       class="btn btn-sm btn-outline-info" title="Bon d'analyse">
                                        <i class="fas fa-flask"></i>
                                    </a>
                                    <a href="{{ route('medecin.bons-analyse.print-by-patient', $consultation->id_patient) }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="Imprimer le bon d'analyse"
                                       target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-stethoscope fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucune consultation trouvée</p>
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
            {{ $consultations->appends(['search' => $search ?? '', 'statut' => $statut ?? '', 'date_debut' => $dateDebut ?? '', 'date_fin' => $dateFin ?? ''])->links() }}
        </div>
    </div>
</div>

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
    .stat-trend.warning {
        color: #ffc107;
    }
    .stat-trend.down {
        color: #dc3545;
    }
</style>
@endpush
@endsection