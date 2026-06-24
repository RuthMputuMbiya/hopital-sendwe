<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\Maladie;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    // Supprimez le constructeur avec middleware()
    // La vérification se fait déjà dans les routes

    /**
     * Tableau de bord des statistiques
     */
    public function index()
    {
        // Statistiques globales
        $stats = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_consultations' => Consultation::count(),
            'total_examens' => Examen::count(),
            'total_maladies' => Maladie::count(),
            'consultations_mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'patients_mois' => Patient::whereMonth('created_at', now()->month)->count(),
            'examens_mois' => Examen::whereMonth('date_examen', now()->month)->count(),
        ];
        
        // Évolution mensuelle des consultations (12 derniers mois)
        $consultationsEvolution = [];
        $moisLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $mois = now()->subMonths($i);
            $moisLabels[] = ucfirst($mois->locale('fr')->isoFormat('MMM YYYY'));
            
            $total = Consultation::whereYear('date_consultation', $mois->year)
                ->whereMonth('date_consultation', $mois->month)
                ->count();
            
            $consultationsEvolution[] = $total;
        }
        
        // Répartition des utilisateurs par rôle
        $usersByRole = Role::withCount('users')->get();
        
        // Maladies les plus fréquentes
        $topMaladies = Maladie::withCount('consultations')
            ->orderBy('consultations_count', 'desc')
            ->limit(10)
            ->get();
        
        // Consultations par médecin
        $topMedecins = User::where('role_id', 2)
            ->withCount('consultationsEffectuees')
            ->orderBy('consultations_effectuees_count', 'desc')
            ->limit(5)
            ->get();
        
        // Examens par laborantin
        $topLaborantins = User::where('role_id', 4)
            ->withCount('examensEffectues')
            ->orderBy('examens_effectues_count', 'desc')
            ->limit(5)
            ->get();
        
        // Tendances (comparaison mois précédent)
        $moisPrecedent = now()->subMonth();
        $tendances = [
            'consultations' => $this->calculerTendance(
                Consultation::whereMonth('date_consultation', now()->month)->count(),
                Consultation::whereMonth('date_consultation', $moisPrecedent->month)->count()
            ),
            'patients' => $this->calculerTendance(
                Patient::whereMonth('created_at', now()->month)->count(),
                Patient::whereMonth('created_at', $moisPrecedent->month)->count()
            ),
            'examens' => $this->calculerTendance(
                Examen::whereMonth('date_examen', now()->month)->count(),
                Examen::whereMonth('date_examen', $moisPrecedent->month)->count()
            ),
        ];
        
        return view('gestionnaire.statistiques.index', compact(
            'stats', 'consultationsEvolution', 'moisLabels', 
            'usersByRole', 'topMaladies', 'topMedecins', 
            'topLaborantins', 'tendances'
        ));
    }

    /**
     * Statistiques des consultations
     */
    public function consultations()
    {
        $stats = [
            'total' => Consultation::count(),
            'mois_actuel' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'mois_precedent' => Consultation::whereMonth('date_consultation', now()->subMonth()->month)->count(),
            'aujourdhui' => Consultation::whereDate('date_consultation', today())->count(),
            'moyenne_par_jour' => round(Consultation::count() / max(Consultation::distinct('date_consultation')->count('date_consultation'), 1), 1),
        ];
        
        // Consultations par jour (30 derniers jours)
        $consultationsParJour = Consultation::select(
                DB::raw('DATE(date_consultation) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('date_consultation', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        // Consultations par maladie
        $consultationsParMaladie = Maladie::withCount('consultations')
            ->having('consultations_count', '>', 0)
            ->orderBy('consultations_count', 'desc')
            ->get();
        
        // Consultations par médecin
        $consultationsParMedecin = User::where('role_id', 2)
            ->withCount('consultationsEffectuees')
            ->orderBy('consultations_effectuees_count', 'desc')
            ->get();
        
        return view('gestionnaire.statistiques.consultations', compact(
            'stats', 'consultationsParJour', 'consultationsParMaladie', 'consultationsParMedecin'
        ));
    }

    /**
     * Statistiques des patients
     */
    public function patients()
    {
        $stats = [
            'total' => Patient::count(),
            'mois_actuel' => Patient::whereMonth('created_at', now()->month)->count(),
            'mois_precedent' => Patient::whereMonth('created_at', now()->subMonth()->month)->count(),
            'femmes' => Patient::where('sexe', 'F')->count(),
            'hommes' => Patient::where('sexe', 'M')->count(),
            'avec_examens' => Patient::has('examens')->count(),
            'avec_consultations' => Patient::has('consultations')->count(),
        ];
        
        // Patients par mois (12 derniers mois)
        $patientsParMois = Patient::select(
                DB::raw('YEAR(created_at) as annee'),
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
        
        // Patients par tranche d'âge
        $patientsParAge = [
            '0-10' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 0 AND 10')->count(),
            '11-20' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 11 AND 20')->count(),
            '21-30' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 21 AND 30')->count(),
            '31-40' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 31 AND 40')->count(),
            '41-50' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 41 AND 50')->count(),
            '51+' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) > 50')->count(),
        ];
        
        return view('gestionnaire.statistiques.patients', compact('stats', 'patientsParMois', 'patientsParAge'));
    }

    /**
     * Statistiques des examens
     */
    public function examens()
    {
        $stats = [
            'total' => Examen::count(),
            'termines' => Examen::where('statut', 'termine')->count(),
            'en_attente' => Examen::where('statut', 'en_attente')->count(),
            'annules' => Examen::where('statut', 'annule')->count(),
            'taux_realisation' => round(Examen::where('statut', 'termine')->count() / max(Examen::count(), 1) * 100, 1),
        ];
        
        // Examens par type
        $examensParType = Examen::select('type_examen', DB::raw('COUNT(*) as total'))
            ->groupBy('type_examen')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Examens par mois
        $examensParMois = Examen::select(
                DB::raw('YEAR(date_examen) as annee'),
                DB::raw('MONTH(date_examen) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->where('date_examen', '>=', now()->subMonths(12))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
        
        // Examens par laborantin
        $examensParLaborantin = User::where('role_id', 4)
            ->withCount('examensEffectues')
            ->orderBy('examens_effectues_count', 'desc')
            ->get();
        
        return view('gestionnaire.statistiques.examens', compact(
            'stats', 'examensParType', 'examensParMois', 'examensParLaborantin'
        ));
    }

    /**
     * Exporter les statistiques
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf');
        $periode = $request->get('periode', 'mois');
        
        // Logique d'export à implémenter selon vos besoins
        // Peut utiliser maatwebsite/excel ou dompdf
        
        return back()->with('info', 'Fonctionnalité d\'export en développement.');
    }

    /**
     * Calculer la tendance entre deux valeurs
     */
    private function calculerTendance($actuel, $precedent)
    {
        if ($precedent == 0) {
            return ['pourcentage' => 100, 'direction' => 'up', 'label' => 'en hausse'];
        }
        
        $pourcentage = round((($actuel - $precedent) / $precedent) * 100, 1);
        $direction = $pourcentage >= 0 ? 'up' : 'down';
        $label = $pourcentage >= 0 ? 'en hausse' : 'en baisse';
        
        return [
            'pourcentage' => abs($pourcentage),
            'direction' => $direction,
            'label' => $label
        ];
    }
}