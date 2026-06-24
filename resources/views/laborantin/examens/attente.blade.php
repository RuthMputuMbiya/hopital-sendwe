@extends('layouts.laborantin')

@section('title', 'Examens en attente')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div><h1 class="h2"><i class="fas fa-hourglass-half me-2" style="color: var(--warning);"></i>Examens en attente</h1><p class="text-muted">Examens à traiter</p></div>
        <div><a href="{{ route('laborantin.examens.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Nouvel examen</a></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6"><div class="stat-card"><div class="stat-number">{{ number_format($stats['total_attente']) }}</div><div class="stat-label">En attente</div></div></div>
        <div class="col-md-6"><div class="stat-card"><div class="stat-number">{{ number_format($stats['urgent']) }}</div><div class="stat-label">Urgents</div><div class="stat-trend danger">Priorité élevée</div></div></div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-list me-2"></i>Liste des examens à traiter</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>N° Examen</th><th>Patient</th><th>Type</th><th>Date demande</th><th>Priorité</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($examens as $examen)
                        <tr>
                            <td><code>{{ $examen->numero_examen }}</code></td>
                            <td><strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong><br><small>{{ $examen->patient->numero_fiche ?? '' }}</small></td>
                            <td>{{ $examen->type_examen }}</td>
                            <td>{{ $examen->created_at->format('d/m/Y H:i') }}</td>
                            <td>@if($examen->priorite == 'urgent')<span class="badge badge-danger">URGENT</span>@else<span class="badge badge-info">Normal</span>@endif</td>
                            <td>
                                <a href="{{ route('laborantin.examens.resultat', $examen->id_examen) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Traiter</a>
                                <a href="{{ route('laborantin.examens.show', $examen->id_examen) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">Aucun examen en attente</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $examens->links() }}</div>
    </div>
</div>
@endsection