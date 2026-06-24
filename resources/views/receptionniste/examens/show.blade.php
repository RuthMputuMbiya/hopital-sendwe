@extends('layouts.receptionniste')

@section('title', 'Détail résultat examen')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt me-2 text-primary"></i> Résultat d'examen</h2>
                <div>
                    <a href="{{ route('receptionniste.examens.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                    <a href="{{ route('receptionniste.examens.print', $examen->id_examen) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-2"></i> Imprimer
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>N° Examen:</strong> {{ $examen->numero_examen }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') }}</p>
                            <p><strong>Type:</strong> {{ $examen->type_examen }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Patient:</strong> {{ $examen->patient->nom_complet ?? 'N/A' }}</p>
                            <p><strong>N° Fiche:</strong> {{ $examen->patient->numero_fiche ?? 'N/A' }}</p>
                            <p><strong>Laborantin:</strong> {{ $examen->laborantin->nom_complet ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <hr>
                    <h5 class="text-primary">Résultat</h5>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($examen->resultat)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection