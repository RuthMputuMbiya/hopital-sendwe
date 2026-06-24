@extends('layouts.laborantin')

@section('title', 'Nouvel examen')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
        <div data-aos="fade-right">
            <h1 class="h2 mb-2 fw-bold" style="color: #1a4d6b;">
                <i class="fas fa-plus-circle me-2" style="color: #00bcd4;"></i>
                Nouvel examen
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-microscope me-1"></i> 
                Enregistrement d'une nouvelle analyse médicale
            </p>
        </div>
        <div data-aos="fade-left">
            <a href="{{ route('laborantin.examens.index') }}" class="btn btn-outline-secondary rounded-3 px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-4" data-aos="fade-up">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-info-circle fa-2x" style="color: #1a4d6b;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #1a4d6b;">Informations de l'examen</h4>
                            <p class="text-muted small mb-0">Veuillez remplir tous les champs obligatoires</p>
                        </div>
                    </div>
                    <hr class="mt-3">
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('laborantin.examens.store') }}">
                        @csrf

                        <!-- Patient -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="fas fa-user-circle me-2" style="color: #1a4d6b;"></i>
                                Patient <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <select name="id_patient" class="form-select form-select-lg modern-select @error('id_patient') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Sélectionnez un patient --</option>
                                    @foreach($patients ?? [] as $patient)
                                        <option value="{{ $patient->id_patient ?? $patient->id }}" 
                                            {{ old('id_patient', $selectedPatient->id_patient ?? '') == ($patient->id_patient ?? $patient->id) ? 'selected' : '' }}>
                                            <i class="fas fa-user me-2"></i>
                                            {{ $patient->nom_complet ?? $patient->name ?? 'N/A' }} 
                                            @if(isset($patient->numero_fiche)) 
                                                - <span class="text-muted">{{ $patient->numero_fiche }}</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #1a4d6b;"></i>
                            </div>
                            @error('id_patient') 
                                <div class="invalid-feedback d-block mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i> Sélectionnez le patient concerné par cet examen
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type examen -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-microscope me-2" style="color: #1a4d6b;"></i>
                                    Type d'examen <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <select name="type_examen" class="form-select form-select-lg modern-select @error('type_examen') is-invalid @enderror" required>
                                        <option value="" disabled selected>-- Sélectionnez un type --</option>
                                        <option value="NFS" {{ old('type_examen') == 'NFS' ? 'selected' : '' }}>🔬 NFS (Numération Formule Sanguine)</option>
                                        <option value="Glycémie" {{ old('type_examen') == 'Glycémie' ? 'selected' : '' }}>🩸 Glycémie</option>
                                        <option value="Bilan hépatique" {{ old('type_examen') == 'Bilan hépatique' ? 'selected' : '' }}>🏥 Bilan hépatique</option>
                                        <option value="Bilan rénal" {{ old('type_examen') == 'Bilan rénal' ? 'selected' : '' }}>🩺 Bilan rénal</option>
                                        <option value="Bilan lipidique" {{ old('type_examen') == 'Bilan lipidique' ? 'selected' : '' }}>❤️ Bilan lipidique</option>
                                        <option value="Analyse d'urine" {{ old('type_examen') == 'Analyse d\'urine' ? 'selected' : '' }}>🧪 Analyse d'urine</option>
                                        <option value="Radiologie" {{ old('type_examen') == 'Radiologie' ? 'selected' : '' }}>📷 Radiologie</option>
                                        <option value="Échographie" {{ old('type_examen') == 'Échographie' ? 'selected' : '' }}>🩵 Échographie</option>
                                        <option value="COVID-19" {{ old('type_examen') == 'COVID-19' ? 'selected' : '' }}>🦠 Test COVID-19</option>
                                        <option value="Autre" {{ old('type_examen') == 'Autre' ? 'selected' : '' }}>📋 Autre</option>
                                    </select>
                                    <i class="fas fa-chevron-down position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #1a4d6b;"></i>
                                </div>
                                @error('type_examen') 
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Statut initial -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-flag-checkered me-2" style="color: #1a4d6b;"></i>
                                    Statut initial
                                </label>
                                <div class="position-relative">
                                    <select name="statut" class="form-select form-select-lg modern-select">
                                        <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                                        <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>⚙️ En cours</option>
                                    </select>
                                    <i class="fas fa-chevron-down position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #1a4d6b;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Date -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-calendar-alt me-2" style="color: #1a4d6b;"></i>
                                    Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                                        <i class="fas fa-calendar-day" style="color: #1a4d6b;"></i>
                                    </span>
                                    <input type="date" name="date_examen" 
                                        class="form-control form-control-lg modern-date @error('date_examen') is-invalid @enderror" 
                                        style="border-left: none; border-radius: 0 12px 12px 0;"
                                        value="{{ old('date_examen', date('Y-m-d')) }}" required>
                                </div>
                                @error('date_examen') 
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Heure -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-clock me-2" style="color: #1a4d6b;"></i>
                                    Heure <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                                        <i class="fas fa-hourglass-half" style="color: #1a4d6b;"></i>
                                    </span>
                                    <input type="time" name="heure_examen" 
                                        class="form-control form-control-lg modern-time @error('heure_examen') is-invalid @enderror" 
                                        style="border-left: none; border-radius: 0 12px 12px 0;"
                                        value="{{ old('heure_examen', date('H:i')) }}" required>
                                </div>
                                @error('heure_examen') 
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Maladie associée -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-disease me-2" style="color: #1a4d6b;"></i>
                                    Maladie associée
                                </label>
                                <div class="position-relative">
                                    <select name="id_maladie" class="form-select form-select-lg modern-select">
                                        <option value="" selected disabled>-- Aucune maladie spécifiée --</option>
                                        @foreach($maladies ?? [] as $maladie)
                                            <option value="{{ $maladie->id_maladie ?? $maladie->id }}" 
                                                {{ old('id_maladie') == ($maladie->id_maladie ?? $maladie->id) ? 'selected' : '' }}>
                                                <i class="fas fa-virus me-2"></i>
                                                {{ $maladie->nom_maladie ?? $maladie->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #1a4d6b;"></i>
                                </div>
                                <div class="form-text text-muted mt-1">
                                    <i class="fas fa-info-circle me-1"></i> Optionnel - Laissez vide si non applicable
                                </div>
                            </div>

                            <!-- Priorité -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-exclamation-triangle me-2" style="color: #1a4d6b;"></i>
                                    Priorité
                                </label>
                                <div class="d-flex gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priorite" id="normal" value="normal" 
                                            {{ old('priorite', 'normal') == 'normal' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="normal">
                                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i> Normal
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priorite" id="urgent" value="urgent"
                                            {{ old('priorite') == 'urgent' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="urgent">
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">
                                                <i class="fas fa-exclamation-circle me-1"></i> Urgent
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commentaires -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="fas fa-comment-dots me-2" style="color: #1a4d6b;"></i>
                                Commentaires
                            </label>
                            <textarea name="commentaires" class="form-control modern-textarea @error('commentaires') is-invalid @enderror" 
                                rows="4" placeholder="Informations complémentaires, symptômes, observations particulières...">{{ old('commentaires') }}</textarea>
                            @error('commentaires') 
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Alert info -->
                        <div class="alert alert-info d-flex align-items-center mb-4" style="background: linear-gradient(135deg, #e8f4f8 0%, #d1ecf1 100%); border: none; border-radius: 16px;">
                            <div class="rounded-circle bg-white p-2 me-3 shadow-sm">
                                <i class="fas fa-info-circle fa-2x" style="color: #1a4d6b;"></i>
                            </div>
                            <div>
                                <strong class="d-block" style="color: #1a4d6b;">Information importante</strong>
                                <span class="small">Un numéro d'examen unique sera généré automatiquement après l'enregistrement.</span>
                            </div>
                        </div>

                        <!-- Boutons action -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <button type="reset" class="btn btn-outline-secondary rounded-3 px-4 py-2">
                                <i class="fas fa-undo-alt me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 shadow-sm">
                                <i class="fas fa-save me-2"></i>Enregistrer l'examen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styles modernes pour les selects et inputs */
    .modern-select {
        border-radius: 12px !important;
        border: 2px solid #e9ecef !important;
        padding: 12px 16px !important;
        background-color: white !important;
        transition: all 0.3s ease !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        cursor: pointer !important;
    }
    
    .modern-select:hover {
        border-color: #1a4d6b !important;
        background-color: #f8f9fa !important;
    }
    
    .modern-select:focus {
        border-color: #1a4d6b !important;
        box-shadow: 0 0 0 3px rgba(26, 77, 107, 0.1) !important;
        outline: none !important;
    }
    
    .modern-date, .modern-time {
        border-radius: 12px !important;
        border: 2px solid #e9ecef !important;
        padding: 12px 16px !important;
        transition: all 0.3s ease !important;
    }
    
    .modern-date:hover, .modern-time:hover {
        border-color: #1a4d6b !important;
    }
    
    .modern-date:focus, .modern-time:focus {
        border-color: #1a4d6b !important;
        box-shadow: 0 0 0 3px rgba(26, 77, 107, 0.1) !important;
        outline: none !important;
    }
    
    .modern-textarea {
        border-radius: 12px !important;
        border: 2px solid #e9ecef !important;
        padding: 12px 16px !important;
        transition: all 0.3s ease !important;
    }
    
    .modern-textarea:focus {
        border-color: #1a4d6b !important;
        box-shadow: 0 0 0 3px rgba(26, 77, 107, 0.1) !important;
        outline: none !important;
    }
    
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        border: 2px solid #ddd;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #1a4d6b;
        border-color: #1a4d6b;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1a4d6b 0%, #0f3b53 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(26, 77, 107, 0.3);
    }
    
    .btn-outline-secondary {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        border-color: #1a4d6b;
        background-color: #f8f9fa;
    }
    
    /* Style pour les options des selects */
    select.modern-select option {
        padding: 10px;
        font-size: 14px;
    }
    
    /* Animation AOS personnalisée */
    [data-aos="fade-up"] {
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.6s ease;
    }
    
    [data-aos="fade-up"].aos-animate {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Animation des selects
        $('.modern-select').on('change', function() {
            $(this).css('border-color', '#1a4d6b');
            setTimeout(() => {
                $(this).css('border-color', '#e9ecef');
            }, 500);
        });
        
        // Initialisation AOS
        AOS.init({
            duration: 800,
            once: true
        });
    });
</script>
@endpush
@endsection