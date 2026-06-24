<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\Maladie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur est bien connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Vérifier que l'utilisateur est bien un gestionnaire
        if (Auth::user()->role_id != 1) {
            // Rediriger vers le bon dashboard selon son rôle
            if (Auth::user()->role_id == 2) {
                return redirect()->route('medecin.dashboard');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('receptionniste.dashboard');
            } elseif (Auth::user()->role_id == 4) {
                return redirect()->route('laborantin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        // Statistiques globales
        $stats = [
            'total_medecins' => User::where('role_id', 2)->count(),
            'total_receptionnistes' => User::where('role_id', 3)->count(),
            'total_laborantins' => User::where('role_id', 4)->count(),
            'total_patients' => Patient::count(),
            'total_consultations' => Consultation::count(),
            'total_examens' => Examen::count(),
            'total_maladies' => Maladie::count(),
            'consultations_mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'taux_activite' => $this->calculerTauxActivite(),
        ];

        // Consultations par maladie (top 5)
        $maladiesStats = DB::table('consultations')
            ->join('maladies', 'consultations.id_maladie', '=', 'maladies.id_maladie')
            ->select('maladies.nom_maladie', DB::raw('COUNT(*) as total'))
            ->whereNotNull('consultations.id_maladie')
            ->groupBy('maladies.nom_maladie')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Dernières consultations
        $recentConsultations = Consultation::with(['patient', 'medecin'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Données pour le graphique (6 derniers mois)
        $chartData = [];
        $chartLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mois = now()->subMonths($i);
            $chartLabels[] = ucfirst($mois->locale('fr')->isoFormat('MMMM YYYY'));
            
            $total = Consultation::whereYear('date_consultation', $mois->year)
                ->whereMonth('date_consultation', $mois->month)
                ->count();
            
            $chartData[] = $total;
        }

        return view('gestionnaire.dashboard', compact(
            'stats',
            'maladiesStats',
            'recentConsultations',
            'chartData',
            'chartLabels'
        ));
    }

    /**
     * Calculer le taux d'activité global
     */
    private function calculerTauxActivite()
    {
        $totalConsultations = Consultation::count();
        $totalPatients = Patient::count();
        
        if ($totalPatients == 0) {
            return 0;
        }
        
        $taux = ($totalConsultations / $totalPatients) * 100;
        
        return round(min($taux, 100), 1);
    }
}