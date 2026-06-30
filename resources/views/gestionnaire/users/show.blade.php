@extends('layouts.gestionnaire')

@section('title', 'Détails utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-user-circle me-2" style="color: var(--secondary);"></i>
                Profil utilisateur
            </h1>
            <p class="text-muted mb-0">{{ $user->prenom }} {{ $user->nom }} ({{ $user->matricule }})</p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('gestionnaire.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('gestionnaire.users.edit', $user->id_user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Colonne gauche -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="user-avatar-lg mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #1a4d8c, #2c6e9e); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $user->prenom }} {{ $user->nom }}</h4>
                    <p class="text-muted mb-3">{{ $user->postnom }}</p>
                    
                    <div class="mb-3">
                        @if($user->est_actif)
                            <span class="badge badge-success"><i class="fas fa-check-circle me-1"></i> Compte actif</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-ban me-1"></i> Compte inactif</span>
                        @endif
                        
                        @if($user->role)
                            @if($user->role->id_role == 5)
                                <span class="badge badge-warning ms-1">🩺 {{ $user->role->nom_role }}</span>
                            @else
                                <span class="badge badge-primary ms-1">{{ $user->role->nom_role }}</span>
                            @endif
                        @endif
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Date d'embauche</small>
                                <strong>{{ $user->date_embauche ? $user->date_embauche->format('d/m/Y') : '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Inscrit depuis</small>
                                <strong>{{ $user->created_at->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions rapides -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-cog me-2"></i> Actions rapides
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('gestionnaire.users.reset-password', $user->id_user) }}" 
                              method="POST" 
                              onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->prenom }} {{ $user->nom }} ?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="fas fa-key me-2"></i> Réinitialiser mot de passe
                            </button>
                        </form>

                        <a href="{{ route('gestionnaire.users.toggle-status', $user->id_user) }}" 
                           class="btn btn-outline-{{ $user->est_actif ? 'danger' : 'success' }}"
                           onclick="return confirm('{{ $user->est_actif ? 'Désactiver' : 'Activer' }} ce compte ?')">
                            <i class="fas fa-{{ $user->est_actif ? 'ban' : 'check-circle' }} me-2"></i>
                            {{ $user->est_actif ? 'Désactiver' : 'Activer' }} le compte
                        </a>
                        
                        @if($user->id_user != auth()->id())
                        <form action="{{ route('gestionnaire.users.destroy', $user->id_user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" 
                                    onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                <i class="fas fa-trash me-2"></i> Supprimer le compte
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne droite -->
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-address-card me-2"></i> Informations personnelles
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Matricule</label>
                            <p class="mb-0"><strong>{{ $user->matricule }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Sexe</label>
                            <p class="mb-0">{{ $user->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small mb-1">Nom complet</label>
                            <p class="mb-0"><strong>{{ $user->prenom }} {{ $user->postnom }} {{ $user->nom }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1"><i class="fas fa-envelope me-1"></i> Email</label>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1"><i class="fas fa-phone me-1"></i> Téléphone</label>
                            <p class="mb-0">{{ $user->telephone }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small mb-1"><i class="fas fa-location-dot me-1"></i> Adresse</label>
                            <p class="mb-0">{{ $user->adresse }}</p>
                        </div>
                        @if($user->specialite)
                        <div class="col-md-12">
                            <label class="text-muted small mb-1"><i class="fas fa-stethoscope me-1"></i> Spécialité</label>
                            <p class="mb-0">{{ $user->specialite }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(!empty($stats))
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Statistiques d'activité
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($user->estMedecin())
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['consultations'] ?? 0) }}</h3>
                                <small class="text-muted">Consultations totales</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['patients_vus'] ?? 0) }}</h3>
                                <small class="text-muted">Patients vus</small>
                            </div>
                        </div>
                        @elseif($user->estLaborantin())
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['examens'] ?? 0) }}</h3>
                                <small class="text-muted">Examens réalisés</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['examens_termines'] ?? 0) }}</h3>
                                <small class="text-muted">Examens terminés</small>
                            </div>
                        </div>
                        @elseif($user->estInfirmier())
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['bilans_realises'] ?? 0) }}</h3>
                                <small class="text-muted">Bilans réalisés</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ number_format($stats['patients_pris_en_charge'] ?? 0) }}</h3>
                                <small class="text-muted">Patients pris en charge</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bell me-2"></i> Dernières notifications
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notif)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $notif->titre }}</h6>
                                    <p class="mb-0 small text-muted">{{ $notif->message }}</p>
                                </div>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">Aucune notification</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection