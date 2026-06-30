<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\Maladie;
use App\Models\BonAnalyse;
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
            } elseif (Auth::user()->role_id == 5) {
                return redirect()->route('infirmier.dashboard');
            }
            return redirect()->route('dashboard');
        }

        // ============================================================
        // STATISTIQUES GLOBALES
        // ============================================================
        $stats = [
            // Personnel
            'total_medecins' => User::where('role_id', 2)->count(),
            'total_receptionnistes' => User::where('role_id', 3)->count(),
            'total_laborantins' => User::where('role_id', 4)->count(),
            'total_infirmiers' => User::where('role_id', 5)->count(),
            
            // Patients et consultations
            'total_patients' => Patient::count(),
            'total_consultations' => Consultation::count(),
            'total_examens' => Examen::count(),
            'total_maladies' => Maladie::count(),
            'consultations_mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'taux_activite' => $this->calculerTauxActivite(),
            
            // ✅ STATS SANS BILANS
            // 'total_bilans' => Bilan::count(),  // ❌ SUPPRIMÉ
            'total_bons_analyse' => BonAnalyse::count(),
            'patients_aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'consultations_aujourdhui' => Consultation::whereDate('date_consultation', today())->count(),
        ];

        // ============================================================
        // MALADIES LES PLUS FRÉQUENTES (TOP 5)
        // ============================================================
        $maladiesStats = DB::table('consultations')
            ->join('maladies', 'consultations.id_maladie', '=', 'maladies.id_maladie')
            ->select('maladies.nom_maladie', DB::raw('COUNT(*) as total'))
            ->whereNotNull('consultations.id_maladie')
            ->groupBy('maladies.nom_maladie')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // DERNIÈRES CONSULTATIONS
        // ============================================================
        $recentConsultations = Consultation::with(['patient', 'medecin', 'infirmier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ============================================================
        // GRAPHIQUE (6 DERNIERS MOIS)
        // ============================================================
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

        // ============================================================
        // STATISTIQUES PAR RÔLE
        // ============================================================
        $roleStats = [
            'medecins_actifs' => User::where('role_id', 2)->where('est_actif', true)->count(),
            'receptionnistes_actifs' => User::where('role_id', 3)->where('est_actif', true)->count(),
            'laborantins_actifs' => User::where('role_id', 4)->where('est_actif', true)->count(),
            'infirmiers_actifs' => User::where('role_id', 5)->where('est_actif', true)->count(),
        ];

        return view('gestionnaire.dashboard', compact(
            'stats',
            'maladiesStats',
            'recentConsultations',
            'chartData',
            'chartLabels',
            'roleStats'
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