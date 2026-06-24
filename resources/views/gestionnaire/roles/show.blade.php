@extends('layouts.gestionnaire')

@section('title', 'Détails du rôle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-tag me-2" style="color: var(--secondary);"></i>
                Détails du rôle : {{ $role->nom_role }}
            </h1>
            <p class="text-muted mb-0">Informations complètes du rôle</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('gestionnaire.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            @if(!$isSystemRole)
                <a href="{{ route('gestionnaire.roles.edit', $role->id_role) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> Modifier
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Carte informations -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tag fa-3x text-white"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $role->nom_role }}</h4>
                    <p class="text-muted mb-3">Rôle #{{ $role->id_role }}</p>
                    
                    <div class="mb-3">
                        @if($isSystemRole)
                            <span class="badge badge-info"><i class="fas fa-crown me-1"></i> Rôle système</span>
                        @else
                            <span class="badge badge-success"><i class="fas fa-plus-circle me-1"></i> Rôle personnalisé</span>
                        @endif
                        
                        <span class="badge bg-primary ms-1">{{ $role->users_count }} utilisateur(s)</span>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Créé le</small>
                                <strong>{{ $role->created_at->format('d/m/Y') }}</strong>
                                <small class="text-muted d-block">{{ $role->created_at->format('H:i') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Modifié le</small>
                                <strong>{{ $role->updated_at->format('d/m/Y') }}</strong>
                                <small class="text-muted d-block">{{ $role->updated_at->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Description et utilisateurs -->
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-align-left me-2"></i> Description
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $role->description ?? 'Aucune description fournie.' }}</p>
                </div>
            </div>
            
            <!-- Utilisateurs avec ce rôle -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-2"></i> Utilisateurs avec ce rôle
                    <span class="badge bg-primary ms-2">{{ $users->total() }} utilisateurs</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom complet</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Date d'ajout</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td><code>{{ $user->matricule }}</code></td>
                                    <td>
                                        <strong>{{ $user->prenom }} {{ $user->nom }}</strong><br>
                                        <small class="text-muted">{{ $user->postnom }}</small>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->est_actif)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Aucun utilisateur n'a ce rôle</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection