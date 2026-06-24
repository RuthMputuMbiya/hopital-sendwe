@extends('layouts.gestionnaire')

@section('title', 'Réinitialiser mot de passe')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">
            <i class="fas fa-key me-2" style="color: var(--warning);"></i>
            Réinitialiser le mot de passe
        </h1>
        <a href="{{ route('gestionnaire.users.show', $user->id_user) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmation
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-user fa-3x text-primary mb-3 d-block"></i>
                        <h5>{{ $user->prenom }} {{ $user->nom }}</h5>
                        <p class="text-muted">{{ $user->matricule }}</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Êtes-vous sûr de vouloir réinitialiser le mot de passe ?
                        <br>
                        <small>Le nouveau mot de passe sera : <strong>Password@123</strong></small>
                    </div>

                    <form action="{{ route('gestionnaire.users.reset-password', $user->id_user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i> Oui, réinitialiser
                        </button>
                        <a href="{{ route('gestionnaire.users.show', $user->id_user) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection