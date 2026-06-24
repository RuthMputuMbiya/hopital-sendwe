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
                <div class="stat-number">{{ number_format($stats['en_cours'] ?? 0) }}</div>
                <div class="stat-label">En cours</div>
                <div class="stat-trend warning">À traiter</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['termines'] ?? 0) }}</div>
                <div class="stat-label">Terminées</div>
                <div class="stat-trend up">Ce mois: {{ $stats['ce_mois'] ?? 0 }}</div>
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
                        <option value="en_cours" {{ ($statut ?? '') == 'en_cours' ? 'selected' : '' }}>En cours</option>
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
                            <th>Diagnostic</th>
                            <th>Maladie</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $consultation)
                        <tr>
                            <td><code>{{ $consultation->numero_consultacion ?? 'N/A' }}</code></td>
                            <td>
                                {{ isset($consultation->date_consultation) ? $consultation->date_consultation->format('d/m/Y') : 'N/A' }}<br>
                                <small class="text-muted">{{ $consultation->heure_consultation ?? '' }}</small>
                            </td>
                            <td>
                                <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $consultation->patient->numero_fiche ?? '' }}</small>
                            </td>
                            <td>{{ Str::limit($consultation->diagnostic ?? 'N/A', 40) }}</td>
                            <td>
                                @if($consultation->maladie)
                                    <span class="badge bg-info">{{ $consultation->maladie->nom_maladie }}</span>
                                @else
                                    <span class="badge bg-secondary">Non spécifiée</span>
                                @endif
                            </td>
                            <td>
                                @if($consultation->statut == 'en_cours')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> En cours</span>
                                @elseif($consultation->statut == 'termine')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Terminé</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Annulé</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('medecin.consultations.show', $consultation->id_consultation) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($consultation->statut != 'termine')
                                    <a href="{{ route('medecin.consultations.edit', $consultation->id_consultation) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('medecin.bons-analyse.create', $consultation->id_patient) }}" 
                                       class="btn btn-sm btn-outline-info" title="Bon d'analyse">
                                        <i class="fas fa-flask"></i>
                                    </a>
                                    <!-- ⭐ BOUTON IMPRIMER CORRIGÉ ⭐ -->
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
@endsection