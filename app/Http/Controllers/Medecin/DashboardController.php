<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur est bien connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Vérifier que l'utilisateur est bien un médecin
        if (Auth::user()->role_id != 2) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('gestionnaire.dashboard');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('receptionniste.dashboard');
            } elseif (Auth::user()->role_id == 4) {
                return redirect()->route('laborantin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $userId = Auth::id();

        $stats = [
            'total_consultations' => Consultation::where('medecin_id', $userId)->count(),
            'consultations_aujourdhui' => Consultation::where('medecin_id', $userId)->whereDate('date_consultation', today())->count(),
            'patients_vus' => Consultation::where('medecin_id', $userId)->distinct('id_patient')->count('id_patient'),
        ];

        $patientsEnAttente = Consultation::with(['patient'])
            ->where('medecin_id', $userId)
            ->where('statut', 'en_cours')
            ->orderBy('date_consultation', 'desc')
            ->get();

        $recentConsultations = Consultation::with(['patient'])
            ->where('medecin_id', $userId)
            ->orderBy('date_consultation', 'desc')
            ->limit(5)
            ->get();

        return view('medecin.dashboard', compact('stats', 'patientsEnAttente', 'recentConsultations'));
    }
}