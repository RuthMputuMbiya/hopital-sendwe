@extends('layouts.laborantin')

@section('title', 'Analyse détaillée')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div><h1 class="h2"><i class="fas fa-chart-line me-2" style="color: var(--secondary);"></i>Analyse détaillée</h1><p class="text-muted">{{ $examen->numero_examen }} - {{ $examen->type_examen }}</p></div>
        <div><a href="{{ route('laborantin.examens.show', $examen->id_examen) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a></div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4"><div class="card-header"><i class="fas fa-info-circle me-2"></i>Informations</div>
            <div class="card-body">
                <p><strong>Patient:</strong> {{ $examen->patient->nom_complet ?? 'N/A' }}</p>
                <p><strong>Type examen:</strong> {{ $examen->type_examen }}</p>
                <p><strong>Date:</strong> {{ $examen->date_examen->format('d/m/Y') }}</p>
                <p><strong>Statut:</strong> <span class="badge badge-success">Terminé</span></p>
            </div></div>

            @if($valeursNormales)
            <div class="card"><div class="card-header"><i class="fas fa-table me-2"></i>Valeurs normales de référence</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">@foreach($valeursNormales as $param => $val)<tr><td>{{ $param }}</td><td>{{ $val['min'] }} - {{ $val['max'] }} {{ $val['unite'] }}</td></tr>@endforeach</table>
            </div></div>
            @endif
        </div>

        <div class="col-md-7">
            <div class="card"><div class="card-header"><i class="fas fa-chart-line me-2"></i>Résultats</div>
            <div class="card-body"><div class="p-3 bg-light rounded">{!! nl2br(e($examen->resultat)) !!}</div></div></div>

            <div class="card mt-4"><div class="card-header"><i class="fas fa-stethoscope me-2"></i>Interprétation</div>
            <div class="card-body"><div class="alert alert-info">{{ $interpretation }}</div></div></div>
        </div>
    </div>
</div>
@endsection