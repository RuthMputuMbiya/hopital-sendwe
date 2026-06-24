@extends('layouts.receptionniste')

@section('title', 'Agenda')

@section('page-title', 'Agenda des rendez-vous')
@section('page-subtitle', 'Gestion des consultations et examens')

@section('content')
<div class="container-fluid">
    <!-- Barre d'actions -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('receptionniste.agenda.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Vue</label>
                    <select name="vue" class="form-select">
                        <option value="jour" {{ $vue == 'jour' ? 'selected' : '' }}>Jour</option>
                        <option value="semaine" {{ $vue == 'semaine' ? 'selected' : '' }}>Semaine</option>
                        <option value="mois" {{ $vue == 'mois' ? 'selected' : '' }}>Mois</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-calendar-alt me-2"></i> Afficher
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('receptionniste.agenda.create') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-plus-circle me-2"></i> Nouveau RDV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number">{{ $stats['total_consultations'] }}</div>
                <div class="stat-label">Consultations</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-microscope"></i></div>
                <div class="stat-number">{{ $stats['total_examens'] }}</div>
                <div class="stat-label">Examens</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">{{ $stats['patients_attendus'] }}</div>
                <div class="stat-label">Patients attendus</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ $stats['examens_attente'] }}</div>
                <div class="stat-label">Examens en attente</div>
            </div>
        </div>
    </div>

    <!-- VUE JOUR -->
    @if($vue == 'jour')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-2"></i> Consultations du {{ $date->format('d/m/Y') }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Heure</th>
                                    <th>Patient</th>
                                    <th>Médecin</th>
                                    <th>Motif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultationsJour as $consultation)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $consultation->heure_consultation }}</span></td>
                                    <td>
                                        <strong>{{ $consultation->patient->nom_complet ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $consultation->patient->numero_fiche ?? '' }}</small>
                                    </td>
                                    <td>{{ $consultation->medecin->nom_complet ?? 'N/A' }}</small></td>
                                    <td>{{ Str::limit($consultation->motif, 40) }}</small></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('receptionniste.agenda.edit', $consultation->id_consultation) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('receptionniste.agenda.destroy', $consultation->id_consultation) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Annuler ce rendez-vous ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </table>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Aucune consultation ce jour</small></td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-microscope me-2"></i> Examens du {{ $date->format('d/m/Y') }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Heure</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Laborantin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examensJour as $examen)
                                <tr>
                                    <td><span class="badge bg-info">{{ $examen->heure_examen }}</span></small></td>
                                    <td><strong>{{ $examen->patient->nom_complet ?? 'N/A' }}</strong></small></td>
                                    <td>{{ $examen->type_examen }}</small></td>
                                    <td>{{ $examen->laborantin->nom_complet ?? 'N/A' }}</small></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4">Aucun examen ce jour</small></td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- VUE SEMAINE -->
    @if($vue == 'semaine' && !empty($semaine))
    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-week me-2"></i> Semaine du {{ $semaine[0]['date']->format('d/m/Y') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Consultations</th>
                            <th>Examens</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semaine as $jour)
                        <tr>
                            <td>
                                <strong>{{ $jour['date']->translatedFormat('l') }}</strong><br>
                                <small>{{ $jour['date']->format('d/m/Y') }}</small>
                            </td>
                            <td><span class="badge bg-primary">{{ $jour['consultations'] }}</span></td>
                            <td><span class="badge bg-info">{{ $jour['examens'] }}</span></td>
                            <td><span class="badge bg-success">{{ $jour['consultations'] + $jour['examens'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- VUE MOIS -->
    @if($vue == 'mois' && !empty($mois))
    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-2"></i> Mois de {{ $date->translatedFormat('F Y') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Consultations</th>
                            <th>Examens</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mois as $jour)
                        <tr>
                            <td>
                                <strong>{{ $jour['date']->format('d/m/Y') }}</strong><br>
                                <small>{{ $jour['date']->translatedFormat('l') }}</small>
                            </td>
                            <td><span class="badge bg-primary">{{ $jour['consultations'] }}</span></td>
                            <td><span class="badge bg-info">{{ $jour['examens'] }}</span></td>
                            <td><span class="badge bg-success">{{ $jour['consultations'] + $jour['examens'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection