@extends('layouts.infirmier')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-tachometer-alt me-2" style="color: var(--secondary);"></i>
                Tableau de bord - Infirmier
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} | 
                <i class="fas fa-clock ms-2 me-1"></i> 
                {{ now()->format('H:i') }}
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('infirmier.patients.attente') }}" class="btn btn-primary">
                <i class="fas fa-clock me-2"></i> Patients en attente
            </a>
        </div>
    </div>

    <!-- ========== STATISTIQUES ========== -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock text-warning"></i></div>
                <div class="stat-number text-warning">{{ number_format($stats['en_attente']) }}</div>
                <div class="stat-label">En attente de bilan</div>
                <div class="stat-trend up"><i class="fas fa-user-plus me-1"></i> À prendre en charge</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle text-info"></i></div>
                <div class="stat-number text-info">{{ number_format($stats['bilan_fait']) }}</div>
                <div class="stat-label">Bilan fait</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> En attente médecin</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-stethoscope text-primary"></i></div>
                <div class="stat-number text-primary">{{ number_format($stats['en_consultation']) }}</div>
                <div class="stat-label">En consultation</div>
                <div class="stat-trend stable"><i class="fas fa-minus me-1"></i> Chez le médecin</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical text-success"></i></div>
                <div class="stat-number text-success">{{ number_format($stats['mes_bilans'] ?? 0) }}</div>
                <div class="stat-label">Mes bilans réalisés</div>
                <div class="stat-trend up"><i class="fas fa-calendar-day me-1"></i> Total</div>
            </div>
        </div>
    </div>

    <!-- ========== PATIENTS EN ATTENTE ========== -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-clock me-2 text-warning"></i>
                            <span class="fw-bold">Patients en attente de bilan</span>
                        </div>
                        <span class="badge bg-warning rounded-pill px-3 py-2">{{ $patientsAttente->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>N° Fiche</th>
                                    <th>Médecin</th>
                                    <th>Arrivé</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientsAttente as $consultation)
                                <tr>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet }}</strong>
                                    </td>
                                    <td><code>{{ $consultation->patient->numero_fiche }}</code></td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'Non attribué' }}</td>
                                    <td>{{ $consultation->created_at->format('H:i') }}</td>
                                    <td>
                                        <a href="{{ route('infirmier.patients.bilan', $consultation->patient->id_patient) }}" 
                                           class="btn btn-sm btn-primary rounded-pill">
                                            <i class="fas fa-notes-medical me-1"></i> Bilan
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x d-block mb-2 text-success"></i>
                                        <span class="fw-semibold">Aucun patient en attente</span>
                                        <br><small>Tous les patients ont été pris en charge</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== PATIENTS AVEC BILAN FAIT ========== -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-check-circle me-2 text-info"></i>
                            <span class="fw-bold">Bilan fait - En attente médecin</span>
                        </div>
                        <span class="badge bg-info rounded-pill px-3 py-2">{{ $patientsBilanFait->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>N° Fiche</th>
                                    <th>Médecin</th>
                                    <th>Bilan fait</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientsBilanFait as $consultation)
                                <tr>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet }}</strong>
                                    </td>
                                    <td><code>{{ $consultation->patient->numero_fiche }}</code></td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'Non attribué' }}</td>
                                    <td>{{ $consultation->updated_at->format('H:i') }}</td>
                                    <td>
                                        <a href="{{ route('infirmier.patients.show', $consultation->patient->id_patient) }}" 
                                           class="btn btn-sm btn-outline-info rounded-pill">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-hourglass fa-2x d-block mb-2 text-muted"></i>
                                        <span class="fw-semibold">Aucun bilan en attente de médecin</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== DERNIERS BILANS ========== -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-history me-2 text-primary"></i>
                            <span class="fw-bold">Mes derniers bilans</span>
                        </div>
                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $derniersBilans->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>N° Fiche</th>
                                    <th>Poids</th>
                                    <th>IMC</th>
                                    <th>Temp.</th>
                                    <th>Tension</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($derniersBilans as $bilan)
                                <tr>
                                    <td>{{ $bilan->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <strong>{{ $bilan->consultation->patient->nom_complet }}</strong>
                                    </td>
                                    <td><code>{{ $bilan->consultation->patient->numero_fiche }}</code></td>
                                    <td>{{ $bilan->poids ?? '-' }} kg</td>
                                    <td>
                                        <span class="fw-bold text-{{ $bilan->imc_color ?? 'secondary' }}">
                                            {{ $bilan->imc ?? '-' }}
                                        </span>
                                        <br><small class="text-muted">{{ $bilan->imc_status ?? '-' }}</small>
                                    </td>
                                    <td>{{ $bilan->temperature ?? '-' }} °C</td>
                                    <td>{{ $bilan->tension_arterielle ?? '-' }}</td>
                                    <td>
                                        @if($bilan->est_transmis)
                                            <span class="badge bg-success">✅ Transmis</span>
                                        @else
                                            <span class="badge bg-warning">⏳ En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('infirmier.bilans.show', $bilan->id_bilan) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-notes-medical fa-2x d-block mb-2"></i>
                                        <span class="fw-semibold">Aucun bilan réalisé</span>
                                        <br><small>Commencez à faire des bilans pour voir l'historique ici</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
    .stat-trend {
        font-size: 10px;
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px solid #e9ecef;
    }
    .stat-trend.up { color: #28a745; }
    .stat-trend.down { color: #dc3545; }
    .stat-trend.stable { color: #f39c12; }

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