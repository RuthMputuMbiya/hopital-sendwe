<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    /**
     * Afficher les statistiques
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'mois'); // semaine, mois, annee
        
        // Statistiques patients
        $statsPatients = [
            'total' => Patient::count(),
            'mois_actuel' => Patient::whereMonth('created_at', now()->month)->count(),
            'mois_precedent' => Patient::whereMonth('created_at', now()->subMonth()->month)->count(),
            'femmes' => Patient::where('sexe', 'F')->count(),
            'hommes' => Patient::where('sexe', 'M')->count(),
            'enfants' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) < 18')->count(),
            'adultes' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) BETWEEN 18 AND 65')->count(),
            'seniors' => Patient::whereRaw('YEAR(CURDATE()) - YEAR(date_naissance) > 65')->count(),
        ];
        
        // Statistiques consultations
        $statsConsultations = [
            'total' => Consultation::count(),
            'aujourdhui' => Consultation::whereDate('date_consultation', today())->count(),
            'semaine' => Consultation::whereBetween('date_consultation', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'annee' => Consultation::whereYear('date_consultation', now()->year)->count(),
        ];
        
        // Statistiques examens
        $statsExamens = [
            'total' => Examen::count(),
            'termines' => Examen::where('statut', 'termine')->count(),
            'en_attente' => Examen::where('statut', 'en_attente')->count(),
            'taux_realisation' => round(Examen::where('statut', 'termine')->count() / max(Examen::count(), 1) * 100, 1),
        ];
        
        // Évolution mensuelle des patients (12 derniers mois)
        $evolutionPatients = [];
        $evolutionConsultations = [];
        $moisLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisLabels[] = ucfirst($date->locale('fr')->isoFormat('MMM YYYY'));
            
            $evolutionPatients[] = Patient::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $evolutionConsultations[] = Consultation::whereYear('date_consultation', $date->year)
                ->whereMonth('date_consultation', $date->month)
                ->count();
        }
        
        // Top médecins
        $topMedecins = User::where('role_id', 2)
            ->withCount('consultationsEffectuees')
            ->orderBy('consultations_effectuees_count', 'desc')
            ->limit(5)
            ->get();
        
        // Top examens
        $topExamens = Examen::select('type_examen', DB::raw('COUNT(*) as total'))
            ->groupBy('type_examen')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Répartition par jour de la semaine
        $repartitionSemaine = [
            'Lundi' => Consultation::whereDayOfWeek('date_consultation', 1)->count(),
            'Mardi' => Consultation::whereDayOfWeek('date_consultation', 2)->count(),
            'Mercredi' => Consultation::whereDayOfWeek('date_consultation', 3)->count(),
            'Jeudi' => Consultation::whereDayOfWeek('date_consultation', 4)->count(),
            'Vendredi' => Consultation::whereDayOfWeek('date_consultation', 5)->count(),
            'Samedi' => Consultation::whereDayOfWeek('date_consultation', 6)->count(),
            'Dimanche' => Consultation::whereDayOfWeek('date_consultation', 0)->count(),
        ];
        
        return view('receptionniste.statistiques.index', compact(
            'statsPatients', 'statsConsultations', 'statsExamens',
            'evolutionPatients', 'evolutionConsultations', 'moisLabels',
            'topMedecins', 'topExamens', 'repartitionSemaine', 'periode'
        ));
    }
}