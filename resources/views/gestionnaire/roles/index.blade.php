@extends('layouts.gestionnaire')

@section('title', 'Gestion des rôles')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-tags me-2" style="color: var(--secondary);"></i>
                Gestion des rôles
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-shield-alt me-1"></i> Gérez les rôles et permissions des utilisateurs
            </p>
        </div>
        <div data-aos="fade-left">
            <div class="d-flex gap-2">
                <a href="{{ route('gestionnaire.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> Nouveau rôle
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tags"></i></div>
                <div class="stat-number">{{ $stats['total_roles'] }}</div>
                <div class="stat-label">Total des rôles</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Utilisateurs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-crown"></i></div>
                <div class="stat-number">{{ $stats['system_roles'] }}</div>
                <div class="stat-label">Rôles système</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-plus-circle"></i></div>
                <div class="stat-number">{{ $stats['custom_roles'] }}</div>
                <div class="stat-label">Rôles personnalisés</div>
            </div>
        </div>
    </div>

    <!-- Tableau des rôles -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des rôles
            <span class="badge bg-primary ms-2">{{ $roles->total() }} rôles</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><i class="fas fa-tag me-1"></i> Nom du rôle</th>
                            <th><i class="fas fa-align-left me-1"></i> Description</th>
                            <th><i class="fas fa-users me-1"></i> Utilisateurs</th>
                            <th><i class="fas fa-calendar me-1"></i> Créé le</th>
                            <th><i class="fas fa-cog me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td><code>#{{ $role->id_role }}</code></td>
                            <td>
                                <strong>{{ $role->nom_role }}</strong>
                                @if(in_array($role->id_role, [1,2,3,4]))
                                    <span class="badge badge-info ms-2">Système</span>
                                @endif
                                @if($role->id_role == auth()->user()->role_id)
                                    <span class="badge badge-warning ms-1">Votre rôle</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($role->description, 60) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                            </td>
                            <td>
                                {{ $role->created_at->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $role->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('gestionnaire.roles.show', $role->id_role) }}" 
                                       class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!in_array($role->id_role, [1,2,3,4]))
                                        <a href="{{ route('gestionnaire.roles.edit', $role->id_role) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('gestionnaire.roles.destroy', $role->id_role) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('⚠️ Supprimer définitivement ce rôle ?\n\nCette action est irréversible.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun rôle trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $roles->links() }}
        </div>
    </div>

    <!-- Légende des rôles système -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-2"></i> Rôles système (non modifiables)
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">ID 1</span>
                        <strong>Gestionnaire système</strong>
                    </div>
                    <small class="text-muted">Accès complet à toutes les fonctionnalités</small>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">ID 2</span>
                        <strong>Médecin</strong>
                    </div>
                    <small class="text-muted">Consultations, prescriptions, dossiers patients</small>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">ID 3</span>
                        <strong>Réceptionniste</strong>
                    </div>
                    <small class="text-muted">Enregistrement des patients, rendez-vous</small>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">ID 4</span>
                        <strong>Laborantin</strong>
                    </div>
                    <small class="text-muted">Examens médicaux, analyses</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection