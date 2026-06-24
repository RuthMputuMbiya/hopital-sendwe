@extends('layouts.receptionniste')

@section('title', 'Rapport d\'enregistrement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div><h1 class="h2"><i class="fas fa-chart-bar me-2" style="color: var(--secondary);"></i>Rapport d'enregistrement</h1></div>
        <div>
            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> Imprimer</button>
                <a href="{{ route('receptionniste.enregistrements.export-csv', ['type' => 'patients']) }}" class="btn btn-success"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="{{ route('receptionniste.enregistrements.export-excel', ['type' => 'patients']) }}" class="btn btn-info"><i class="fas fa-file-excel"></i> Excel</a>
            </div>
        </div>
    </div>

    <!-- En-tête rapport -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <h3>Hôpital Sendwe - Lubumbashi</h3>
            <h5>Rapport d'enregistrement des patients</h5>
            <p>Période : {{ $rapport['periode']['libelle'] }}</p>
            <p>Généré le : {{ $rapport['date_generation']->format('d/m/Y H:i') }} par {{ $rapport['utilisateur']->nom_complet }}</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="stat-card"><div class="stat-number">{{ number_format($rapport['patients']['total']) }}</div><div class="stat-label">Total patients</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-number">{{ number_format($rapport['patients']['hommes']) }}</div><div class="stat-label">Hommes</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-number">{{ number_format($rapport['patients']['femmes']) }}</div><div class="stat-label">Femmes</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-number">{{ number_format($rapport['consultations']['total']) }}</div><div class="stat-label">Consultations</div></div></div>
    </div>

    <!-- Détails -->
    <div class="row">
        <div class="col-md-6"><div class="card"><div class="card-header">Par âge</div><div class="card-body">
            @foreach($rapport['patients']['par_age'] as $age => $nb)
            <div class="d-flex justify-content-between"><span>{{ $age }} ans</span><strong>{{ $nb }}</strong></div>
            @endforeach
        </div></div></div>
        <div class="col-md-6"><div class="card"><div class="card-header">Top examens</div><div class="card-body">
            @foreach($rapport['examens']['par_type'] as $examen)
            <div class="d-flex justify-content-between"><span>{{ $examen->type_examen }}</span><span class="badge bg-primary">{{ $examen->total }}</span></div>
            @endforeach
        </div></div></div>
    </div>
</div>
@endsection