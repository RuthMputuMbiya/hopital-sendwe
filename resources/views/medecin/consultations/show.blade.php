@extends('layouts.medecin')

@section('title', 'Détails consultation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div data-aos="fade-right">
            <h1 class="h2 mb-1">
                <i class="fas fa-file-alt me-2" style="color: #1a4d6b;"></i>
                Détails de la consultation
            </h1>
            <p class="text-muted mb-0">{{ $consultation->numero_consultacion ?? 'N/A' }}</p>
        </div>
        <div data-aos="fade-left">
            <div class="btn-group">
                <a href="{{ route('medecin.consultations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
                @if($consultation->statut != 'termine')
                <a href="{{ route('medecin.consultations.edit', $consultation->id_consultation) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> Modifier
                </a>
                @endif
                <a href="{{ route('medecin.bons-analyse.create', $consultation->id_patient) }}" class="btn btn-info">
                    <i class="fas fa-flask me-2"></i> Bon d'analyse
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <!-- Informations patient -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i> Patient
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $consultation->patient->nom_complet ?? 'N/A' }}</h5>
                    <p class="text-muted mb-2">{{ $consultation->patient->numero_fiche ?? 'N/A' }}</p>
                    <hr>
                    <p class="mb-1"><i class="fas fa-calendar me-2 text-primary"></i> Né(e) le: {{ isset($consultation->patient->date_naissance) ? \Carbon\Carbon::parse($consultation->patient->date_naissance)->format('d/m/Y') : 'N/A' }}</p>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i> {{ $consultation->patient->telephone ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-location-dot me-2 text-primary"></i> {{ $consultation->patient->adresse ?? 'N/A' }}</p>
                </div>
            </div>
            
            <!-- Signes vitaux -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-heartbeat me-2"></i> Signes vitaux
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($consultation->tension_arterielle)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-heartbeat text-danger"></i>
                                <div class="small text-muted">Tension</div>
                                <strong>{{ $consultation->tension_arterielle }}</strong>
                            </div>
                        </div>
                        @endif
                        @if($consultation->poids)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-weight-scale text-success"></i>
                                <div class="small text-muted">Poids</div>
                                <strong>{{ $consultation->poids }} kg</strong>
                            </div>
                        </div>
                        @endif
                        @if($consultation->taille)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-ruler text-info"></i>
                                <div class="small text-muted">Taille</div>
                                <strong>{{ $consultation->taille }} cm</strong>
                            </div>
                        </div>
                        @endif
                        @if($consultation->temperature)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-thermometer-half text-warning"></i>
                                <div class="small text-muted">Température</div>
                                <strong>{{ $consultation->temperature }} °C</strong>
                            </div>
                        </div>
                        @endif
                        @if($consultation->frequence_cardiaque)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-heart text-danger"></i>
                                <div class="small text-muted">FC</div>
                                <strong>{{ $consultation->frequence_cardiaque }} bpm</strong>
                            </div>
                        </div>
                        @endif
                        @if($consultation->imc)
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2 text-center">
                                <i class="fas fa-calculator text-primary"></i>
                                <div class="small text-muted">IMC</div>
                                <strong>{{ $consultation->imc }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informations consultation -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informations
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-calendar me-2 text-primary"></i> Date: {{ isset($consultation->date_consultation) ? \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') : 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i> Heure: {{ $consultation->heure_consultation ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-user-md me-2 text-primary"></i> Médecin: Dr. {{ $consultation->medecin->nom_complet ?? 'N/A' }}</p>
                    <p class="mb-0">
                        <i class="fas fa-flag-checkered me-2 text-primary"></i> Statut: 
                        @if($consultation->statut == 'en_cours')
                            <span class="badge bg-warning text-dark">En cours</span>
                        @elseif($consultation->statut == 'termine')
                            <span class="badge bg-success">Terminé</span>
                        @else
                            <span class="badge bg-danger">Annulé</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8" data-aos="fade-up" data-aos-delay="200">
            <!-- Motif et Symptômes -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-question-circle me-2"></i> Motif
                        </div>
                        <div class="card-body">
                            <p>{{ $consultation->motif ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-head-side-medical me-2"></i> Symptômes
                        </div>
                        <div class="card-body">
                            <p>{{ $consultation->symptomes ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Diagnostic -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-stethoscope me-2"></i> Diagnostic
                </div>
                <div class="card-body">
                    <p>{{ $consultation->diagnostic ?? 'N/A' }}</p>
                    @if($consultation->maladie)
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-disease me-2"></i>
                            <strong>Maladie associée:</strong> {{ $consultation->maladie->nom_maladie }}
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Prescription -->
            @if($consultation->prescription)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-prescription-bottle me-2"></i> Prescription
                </div>
                <div class="card-body">
                    <p>{{ $consultation->prescription }}</p>
                </div>
            </div>
            @endif
            
            <!-- Observations -->
            @if($consultation->observations_medecin)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-comment-dots me-2"></i> Observations
                </div>
                <div class="card-body">
                    <p>{{ $consultation->observations_medecin }}</p>
                </div>
            </div>
            @endif
            
            <!-- Examens prescrits -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-microscope me-2"></i> Examens prescrits
                </div>
                <div class="card-body">
                    @if(isset($examens) && $examens->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Résultat</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examens as $examen)
                                <tr>
                                    <td>{{ $examen->type_examen ?? 'N/A' }}</td>
                                    <td>{{ isset($examen->date_examen) ? \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        @if($examen->statut == 'termine')
                                            <span class="badge bg-success">Terminé</span>
                                        @elseif($examen->statut == 'en_attente')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @else
                                            <span class="badge bg-danger">Annulé</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($examen->resultat ?? '-', 50) }}</td>
                                    <td>
                                        <a href="{{ route('medecin.examens.show', $examen->id_examen) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">Aucun examen prescrit</p>
                    @endif
                    
                    <!-- Bouton pour prescrire des examens -->
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#prescrireExamensModal">
                        <i class="fas fa-plus-circle me-1"></i> Prescrire des examens
                    </button>
                </div>
            </div>
            
            <!-- Consultations précédentes -->
            @if(isset($consultationsPrecedentes) && $consultationsPrecedentes->count() > 0)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i> Consultations précédentes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Diagnostic</th>
                                    <th>Médecin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultationsPrecedentes as $prev)
                                <tr>
                                    <td>{{ isset($prev->date_consultation) ? \Carbon\Carbon::parse($prev->date_consultation)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ Str::limit($prev->diagnostic ?? 'N/A', 50) }}</td>
                                    <td>Dr. {{ $prev->medecin->nom_complet ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('medecin.consultations.show', $prev->id_consultation) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Prescrire Examens -->
<div class="modal fade" id="prescrireExamensModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('medecin.consultations.ordonner-examens', $consultation->id_consultation) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-microscope me-2"></i>Prescrire des examens</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="examens-list">
                        <div class="examen-item row g-2 mb-2">
                            <div class="col-7">
                                <select name="examens[0][type]" class="form-select" required>
                                    <option value="">Type examen</option>
                                    <option value="NFS">NFS (Numération Formule Sanguine)</option>
                                    <option value="Glycémie">Glycémie</option>
                                    <option value="Bilan hépatique">Bilan hépatique</option>
                                    <option value="Bilan rénal">Bilan rénal</option>
                                    <option value="Bilan lipidique">Bilan lipidique</option>
                                    <option value="Analyse d'urine">Analyse d'urine</option>
                                    <option value="Radiologie">Radiologie</option>
                                    <option value="Échographie">Échographie</option>
                                    <option value="Scanner">Scanner</option>
                                    <option value="IRM">IRM</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="date" name="examens[0][date]" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-danger remove-examen" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-examen" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Ajouter un examen
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Prescrire</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let examenIndex = 1;
    
    $(document).ready(function() {
        $('#add-examen').on('click', function() {
            const newItem = `
                <div class="examen-item row g-2 mb-2">
                    <div class="col-7">
                        <select name="examens[${examenIndex}][type]" class="form-select" required>
                            <option value="">Type examen</option>
                            <option value="NFS">NFS (Numération Formule Sanguine)</option>
                            <option value="Glycémie">Glycémie</option>
                            <option value="Bilan hépatique">Bilan hépatique</option>
                            <option value="Bilan rénal">Bilan rénal</option>
                            <option value="Bilan lipidique">Bilan lipidique</option>
                            <option value="Analyse d'urine">Analyse d'urine</option>
                            <option value="Radiologie">Radiologie</option>
                            <option value="Échographie">Échographie</option>
                            <option value="Scanner">Scanner</option>
                            <option value="IRM">IRM</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="date" name="examens[${examenIndex}][date]" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger remove-examen">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#examens-list').append(newItem);
            examenIndex++;
            $('.remove-examen').show();
        });
        
        $(document).on('click', '.remove-examen', function() {
            $(this).closest('.examen-item').remove();
            if ($('.examen-item').length === 1) {
                $('.remove-examen').hide();
            }
        });
    });
</script>
@endpush
@endsection