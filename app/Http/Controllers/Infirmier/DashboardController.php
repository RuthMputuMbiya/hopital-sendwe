<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Bilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $infirmierId = Auth::id();
        
        $patientsAttente = Consultation::where('statut_consultation', 'en_attente_infirmier')
            ->with(['patient', 'medecin'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $patientsBilanFait = Consultation::where('statut_consultation', 'bilan_fait')
            ->with(['patient', 'medecin'])
            ->orderBy('updated_at', 'asc')
            ->get();
        
        $patientsEnConsultation = Consultation::where('statut_consultation', 'en_consultation')
            ->with(['patient', 'medecin'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // ✅ STATISTIQUES - AVEC id_infirmier (pas infirmier_id)
        $stats = [
            'en_attente' => Consultation::where('statut_consultation', 'en_attente_infirmier')->count(),
            'bilan_fait' => Consultation::where('statut_consultation', 'bilan_fait')->count(),
            'en_consultation' => Consultation::where('statut_consultation', 'en_consultation')->count(),
            'mes_bilans' => Bilan::where('id_infirmier', $infirmierId)->count(), // ✅ id_infirmier
        ];
        
        // ✅ DERNIERS BILANS - AVEC id_infirmier
        $derniersBilans = Bilan::with(['consultation.patient', 'infirmier'])
            ->where('id_infirmier', $infirmierId) // ✅ id_infirmier
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('infirmier.dashboard', compact(
            'patientsAttente',
            'patientsBilanFait',
            'patientsEnConsultation',
            'stats',
            'derniersBilans'
        ));
    }
}