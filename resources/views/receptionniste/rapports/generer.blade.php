@extends('layouts.receptionniste')

@section('title', 'Générer un rapport')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Générer un rapport</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('receptionniste.rapports.apercu') }}">
                        <div class="mb-3">
                            <label class="form-label">Type de rapport</label>
                            <select name="type_rapport" class="form-select" required>
                                <option value="general">📊 Rapport général</option>
                                <option value="patients">👥 Rapport patients</option>
                                <option value="consultations">🩺 Rapport consultations</option>
                                <option value="examens">🔬 Rapport examens</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Période</label>
                            <select name="periode" id="periode" class="form-select" required>
                                <option value="jour">📅 Aujourd'hui</option>
                                <option value="semaine">📅 Cette semaine</option>
                                <option value="mois" selected>📅 Ce mois</option>
                            </select>
                        </div>
                        
                        <div class="row" id="dateRange" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">Date début</label>
                                <input type="date" name="date_debut" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="date_fin" class="form-control">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('receptionniste.rapports.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Générer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('periode').addEventListener('change', function() {
        var dateRange = document.getElementById('dateRange');
        dateRange.style.display = this.value === 'personnalise' ? 'flex' : 'none';
    });
</script>
@endpush
@endsection