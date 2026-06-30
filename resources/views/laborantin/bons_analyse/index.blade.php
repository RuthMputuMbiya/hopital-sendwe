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
                <div class="stat-icon"><i class="fas fa-flask text-primary"></i></div>
                <div class="stat-number text-primary">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total bons</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half text-warning"></i></div>
                <div class="stat-number text-warning">{{ number_format($stats['en_attente'] ?? 0) }}</div>
                <div class="stat-label">En attente</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-syringe text-info"></i></div>
                <div class="stat-number text-info">{{ number_format($stats['preleve'] ?? 0) }}</div>
                <div class="stat-label">Prélevés</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle text-success"></i></div>
                <div class="stat-number text-success">{{ number_format($stats['termines'] ?? 0) }}</div>
                <div class="stat-label">Terminés</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par numéro ou patient..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ ($statut ?? '') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="preleve" {{ ($statut ?? '') == 'preleve' ? 'selected' : '' }}>🧪 Prélevé</option>
                        <option value="analyse" {{ ($statut ?? '') == 'analyse' ? 'selected' : '' }}>🔬 En analyse</option>
                        <option value="termine" {{ ($statut ?? '') == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="retourne" {{ ($statut ?? '') == 'retourne' ? 'selected' : '' }}>📤 Retourné</option>
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

    <!-- Tableau -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white rounded-top-4 border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2 text-primary"></i>
                    <span class="fw-bold">Bons d'analyse</span>
                    <span class="badge bg-primary ms-2">{{ $bons->total() }}</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                                {{ Carbon\Carbon::parse($bon->date_demande)->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $bon->heure_demande }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bon->priorite == 'urgent' ? 'danger' : ($bon->priorite == 'moyen' ? 'warning' : 'primary') }}">
                                    {{ $bon->priorite == 'urgent' ? '🚨 Urgent' : ($bon->priorite == 'moyen' ? '⚠️ Moyen' : '✅ Normal') }}
                                </span>
                            </td>
                            <td>
                                {!! $bon->statut_badge !!}
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
                                    @if($bon->statut == 'preleve' || $bon->statut == 'analyse')
                                        <a href="{{ route('laborantin.bons-analyse.resultats-form', $bon->id_bon) }}" 
                                           class="btn btn-sm btn-info" title="Saisir résultats">
                                            <i class="fas fa-flask"></i>
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

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        background: #f8f9fa;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-size: 20px;
    }
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .stat-label {
        color: #6c757d;
        font-size: 12px;
        font-weight: 500;
    }
    .card {
        border-radius: 16px !important;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 1px solid #e9ecef !important;
    }
    .table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
        border-bottom: none;
    }
    .table tbody td {
        vertical-align: middle;
        font-size: 13px;
        padding: 10px 12px;
    }
    .badge {
        font-weight: 500;
        font-size: 11px;
        padding: 5px 12px;
    }
    .btn-sm {
        font-size: 12px;
        padding: 4px 14px;
    }
    code {
        background: #f8f9fa;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
    }
</style>
@endpush