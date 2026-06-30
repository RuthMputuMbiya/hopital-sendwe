@extends('layouts.infirmier')

@section('title', 'Tous les bilans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-notes-medical me-2" style="color: var(--secondary);"></i>
                Tous les bilans
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-list me-1"></i> Historique des bilans réalisés
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('infirmier.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-notes-medical text-primary"></i></div>
                <div class="stat-number text-primary">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total bilans</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle text-success"></i></div>
                <div class="stat-number text-success">{{ number_format($stats['transmis'] ?? 0) }}</div>
                <div class="stat-label">Bilans transmis</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock text-warning"></i></div>
                <div class="stat-number text-warning">{{ number_format($stats['non_transmis'] ?? 0) }}</div>
                <div class="stat-label">En attente de transmission</div>
            </div>
        </div>
    </div>

    <!-- Message si aucun bilan -->
    @if(isset($bilans) && $bilans->count() == 0)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted d-block mb-3"></i>
                <h5 class="text-muted">Aucun bilan trouvé</h5>
                <p class="text-muted">Commencez à faire des bilans pour voir l'historique ici.</p>
                <a href="{{ route('infirmier.patients.attente') }}" class="btn btn-primary">
                    <i class="fas fa-clock me-2"></i> Voir les patients en attente
                </a>
            </div>
        </div>
    @endif
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
    }
</style>
@endpush