@extends('layouts.gestionnaire')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-users me-2" style="color: var(--secondary);"></i>
                Gestion des utilisateurs
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-user-cog me-1"></i> Gérez les comptes des médecins, réceptionnistes et laborantins
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('gestionnaire.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i> Nouvel utilisateur
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total utilisateurs</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ number_format($stats['actifs']) }}</div>
                <div class="stat-label">Comptes actifs</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Actifs</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                <div class="stat-number">{{ number_format($stats['medecins']) }}</div>
                <div class="stat-label">Médecins</div>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-microscope"></i></div>
                <div class="stat-number">{{ number_format($stats['laborantins']) }}</div>
                <div class="stat-label">Laborantins</div>
            </div>
        </div>
    </div>

    <div class="card mb-4" data-aos="fade-up">
        <div class="card-body">
            <form method="GET" action="{{ route('gestionnaire.users.index') }}" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Rechercher par nom, matricule, email..." 
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}" {{ $roleFilter == $role->id_role ? 'selected' : '' }}>
                                {{ $role->nom_role }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <i class="fas fa-list me-2"></i> Liste des utilisateurs
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
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Actions</th>
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
                            <td>{{ $user->telephone }}</td>
                            <td>
                                @if($user->role)
                                    <span class="badge badge-primary">{{ $user->role->nom_role }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->est_actif)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-danger">Inactif</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('gestionnaire.users.show', $user->id_user) }}" 
                                       class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('gestionnaire.users.edit', $user->id_user) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('gestionnaire.users.destroy', $user->id_user) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucun utilisateur trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->appends(['search' => $search, 'role' => $roleFilter])->links() }}
        </div>
    </div>
</div>
@endsection