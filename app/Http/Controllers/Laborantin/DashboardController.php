<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur est bien un laborantin (role_id = 4)
        if (Auth::user()->role_id != 4) {
            return redirect()->route('dashboard');
        }

        // Statistiques de base (pour la vue)
        $stats = [
            'examens_total' => BonAnalyse::count(),
            'examens_attente' => BonAnalyse::where('statut', 'en_attente')->count(),
            'examens_cours' => BonAnalyse::whereIn('statut', ['preleve', 'analyse'])->count(),
            'examens_termines' => BonAnalyse::whereIn('statut', ['termine', 'retourne'])->count(),
            'taux_realisation' => BonAnalyse::count() > 0 
                ? round((BonAnalyse::whereIn('statut', ['termine', 'retourne'])->count() / BonAnalyse::count()) * 100, 2) 
                : 0,
        ];

        // Graphique d'évolution (6 derniers mois)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = ucfirst($date->locale('fr')->isoFormat('MMM YYYY'));
            $chartData[] = BonAnalyse::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Top 5 types d'examens (adapté pour la vue)
        $topExamensTypes = BonAnalyse::select('type_echantillon as type_examen', DB::raw('count(*) as total'))
            ->whereNotNull('type_echantillon')
            ->groupBy('type_echantillon')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Examens en attente
        $examensEnAttente = BonAnalyse::with(['patient', 'medecin'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get()
            ->map(function($bon) {
                return (object) [
                    'id_examen' => $bon->id_bon,
                    'id' => $bon->id_bon,
                    'patient' => $bon->patient,
                    'type_examen' => $bon->type_echantillon ?? 'Non spécifié',
                    'date_examen' => $bon->created_at,
                ];
            });

        // Derniers examens réalisés
        $recentExamens = BonAnalyse::with(['patient', 'medecin'])
            ->whereIn('statut', ['termine', 'retourne'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($bon) {
                return (object) [
                    'id_examen' => $bon->id_bon,
                    'id' => $bon->id_bon,
                    'patient' => $bon->patient,
                    'type_examen' => $bon->type_echantillon ?? 'Non spécifié',
                    'date_examen' => $bon->updated_at,
                    'resultat' => $bon->resultats ? true : false,
                ];
            });

        // Performance hebdomadaire (pour la vue)
        $performanceHebdomadaire = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        foreach ($jours as $index => $jour) {
            $date = Carbon::now()->startOfWeek()->addDays($index);
            $realises = BonAnalyse::whereIn('statut', ['termine', 'retourne'])
                ->whereDate('updated_at', $date)
                ->count();
            
            $performanceHebdomadaire[] = [
                'realises' => $realises,
                'prevus' => max($realises, 3)
            ];
        }

        // Évolution mensuelle (pour la vue)
        $evolutionMensuelle = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = BonAnalyse::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $moisPrecedent = BonAnalyse::whereYear('created_at', $date->copy()->subMonth()->year)
                ->whereMonth('created_at', $date->copy()->subMonth()->month)
                ->count();
            
            $variation = $moisPrecedent > 0 ? round((($total - $moisPrecedent) / $moisPrecedent) * 100, 1) : 0;
            
            $evolutionMensuelle[] = [
                'mois' => ucfirst($date->locale('fr')->isoFormat('MMMM YYYY')),
                'total' => $total,
                'variation' => $variation
            ];
        }

        return view('laborantin.dashboard', compact(
            'stats',
            'chartLabels',
            'chartData',
            'topExamensTypes',
            'examensEnAttente',
            'recentExamens',
            'performanceHebdomadaire',
            'evolutionMensuelle'
        ));
    }
}