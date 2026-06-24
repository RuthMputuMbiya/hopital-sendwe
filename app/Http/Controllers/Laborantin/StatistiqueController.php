<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    /**
     * Dashboard des statistiques pour laborantin
     */
    public function index()
    {
        // Statistiques générales des examens
        $stats = [
            'total_examens' => Examen::count(),
            'examens_aujourdhui' => Examen::whereDate('created_at', today())->count(),
            'examens_semaine' => Examen::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'examens_mois' => Examen::whereMonth('created_at', now()->month)->count(),
            'examens_annee' => Examen::whereYear('created_at', now()->year)->count(),
            'examens_termines' => Examen::where('statut', 'termine')->count(),
            'examens_en_attente' => Examen::where('statut', 'en_attente')->count(),
            'examens_en_cours' => Examen::where('statut', 'en_cours')->count(),
        ];

        // Taux de réalisation des examens
        $tauxRealisation = $stats['total_examens'] > 0 
            ? round(($stats['examens_termines'] / $stats['total_examens']) * 100, 2) 
            : 0;

        // Graphique - Examens par type
        $examensParType = Examen::select('type_examen', DB::raw('count(*) as total'))
            ->groupBy('type_examen')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();

        $typeLabels = $examensParType->pluck('type_examen')->toArray();
        $typeData = $examensParType->pluck('total')->toArray();

        // Graphique - Évolution mensuelle des examens (12 derniers mois)
        $evolutionMensuelle = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois = $date->locale('fr')->isoFormat('MMM YYYY');
            $total = Examen::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $evolutionMensuelle[] = [
                'mois' => $mois,
                'total' => $total
            ];
        }
        $evolutionLabels = array_column($evolutionMensuelle, 'mois');
        $evolutionData = array_column($evolutionMensuelle, 'total');

        // Répartition par statut (pour graphique circulaire)
        $repartitionStatut = [
            'En attente' => $stats['examens_en_attente'],
            'En cours' => $stats['examens_en_cours'],
            'Terminés' => $stats['examens_termines']
        ];

        // Top 5 des types d'examens les plus demandés
        $topExamens = Examen::select('type_examen', DB::raw('count(*) as total'))
            ->groupBy('type_examen')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Derniers examens traités
        $derniersExamens = Examen::with(['patient'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Performance par jour (7 derniers jours)
        $performanceHebdo = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $jour = ucfirst($date->locale('fr')->isoFormat('ddd D MMM'));
            $total = Examen::whereDate('created_at', $date)->count();
            $termines = Examen::whereDate('updated_at', $date)->where('statut', 'termine')->count();
            $performanceHebdo[] = [
                'jour' => $jour,
                'total' => $total,
                'termines' => $termines
            ];
        }

        return view('laborantin.statistiques.index', compact(
            'stats', 
            'tauxRealisation', 
            'typeLabels', 
            'typeData',
            'evolutionLabels',
            'evolutionData',
            'repartitionStatut',
            'topExamens',
            'derniersExamens',
            'performanceHebdo'
        ));
    }

    /**
     * Statistiques détaillées par type d'examen
     */
    public function parType(Request $request)
    {
        $typeExamen = $request->get('type');
        
        $query = Examen::query();
        
        if ($typeExamen) {
            $query->where('type_examen', $typeExamen);
        }
        
        $stats = [
            'total' => $query->count(),
            'termines' => (clone $query)->where('statut', 'termine')->count(),
            'en_attente' => (clone $query)->where('statut', 'en_attente')->count(),
            'en_cours' => (clone $query)->where('statut', 'en_cours')->count(),
            'taux_realisation' => $query->count() > 0 
                ? round(((clone $query)->where('statut', 'termine')->count() / $query->count()) * 100, 2)
                : 0
        ];
        
        $examens = $query->with(['patient'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $typesExamens = Examen::select('type_examen')
            ->distinct()
            ->pluck('type_examen');
        
        return view('laborantin.statistiques.par-type', compact('stats', 'examens', 'typesExamens', 'typeExamen'));
    }

    /**
     * Statistiques par période
     */
    public function parPeriode(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        if ($dateDebut && $dateFin) {
            $debut = Carbon::parse($dateDebut)->startOfDay();
            $fin = Carbon::parse($dateFin)->endOfDay();
            $periodeLibelle = "du " . $debut->format('d/m/Y') . " au " . $fin->format('d/m/Y');
        } else {
            switch ($periode) {
                case 'jour':
                    $debut = Carbon::today()->startOfDay();
                    $fin = Carbon::today()->endOfDay();
                    $periodeLibelle = "Aujourd'hui - " . $debut->format('d/m/Y');
                    break;
                case 'semaine':
                    $debut = Carbon::now()->startOfWeek()->startOfDay();
                    $fin = Carbon::now()->endOfWeek()->endOfDay();
                    $periodeLibelle = "Semaine du " . $debut->format('d/m/Y') . " au " . $fin->format('d/m/Y');
                    break;
                case 'annee':
                    $debut = Carbon::now()->startOfYear()->startOfDay();
                    $fin = Carbon::now()->endOfYear()->endOfDay();
                    $periodeLibelle = "Année " . $debut->format('Y');
                    break;
                default:
                    $debut = Carbon::now()->startOfMonth()->startOfDay();
                    $fin = Carbon::now()->endOfMonth()->endOfDay();
                    $periodeLibelle = "Mois de " . $debut->format('F Y');
                    break;
            }
        }
        
        $stats = [
            'total_examens' => Examen::whereBetween('created_at', [$debut, $fin])->count(),
            'examens_termines' => Examen::whereBetween('updated_at', [$debut, $fin])->where('statut', 'termine')->count(),
            'examens_en_attente' => Examen::whereBetween('created_at', [$debut, $fin])->where('statut', 'en_attente')->count(),
            'examens_par_type' => Examen::whereBetween('created_at', [$debut, $fin])
                ->select('type_examen', DB::raw('count(*) as total'))
                ->groupBy('type_examen')
                ->orderBy('total', 'desc')
                ->get(),
            'examens_par_jour' => Examen::whereBetween('created_at', [$debut, $fin])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get(),
        ];
        
        $stats['taux_realisation'] = $stats['total_examens'] > 0 
            ? round(($stats['examens_termines'] / $stats['total_examens']) * 100, 2) 
            : 0;
        
        return view('laborantin.statistiques.par-periode', compact('stats', 'periode', 'dateDebut', 'dateFin', 'periodeLibelle'));
    }

    /**
     * Export des statistiques en PDF
     */
    public function exportPDF(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        if ($dateDebut && $dateFin) {
            $debut = Carbon::parse($dateDebut)->startOfDay();
            $fin = Carbon::parse($dateFin)->endOfDay();
        } else {
            switch ($periode) {
                case 'jour':
                    $debut = Carbon::today()->startOfDay();
                    $fin = Carbon::today()->endOfDay();
                    break;
                case 'semaine':
                    $debut = Carbon::now()->startOfWeek()->startOfDay();
                    $fin = Carbon::now()->endOfWeek()->endOfDay();
                    break;
                case 'annee':
                    $debut = Carbon::now()->startOfYear()->startOfDay();
                    $fin = Carbon::now()->endOfYear()->endOfDay();
                    break;
                default:
                    $debut = Carbon::now()->startOfMonth()->startOfDay();
                    $fin = Carbon::now()->endOfMonth()->endOfDay();
                    break;
            }
        }
        
        $stats = [
            'periode' => [
                'debut' => $debut,
                'fin' => $fin,
                'libelle' => "du " . $debut->format('d/m/Y') . " au " . $fin->format('d/m/Y')
            ],
            'total_examens' => Examen::whereBetween('created_at', [$debut, $fin])->count(),
            'examens_termines' => Examen::whereBetween('updated_at', [$debut, $fin])->where('statut', 'termine')->count(),
            'examens_par_type' => Examen::whereBetween('created_at', [$debut, $fin])
                ->select('type_examen', DB::raw('count(*) as total'))
                ->groupBy('type_examen')
                ->orderBy('total', 'desc')
                ->get(),
            'generated_by' => auth()->user(),
            'generated_at' => now(),
        ];
        
        return view('laborantin.statistiques.export-pdf', compact('stats'));
    }
}