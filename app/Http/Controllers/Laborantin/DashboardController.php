<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur est bien connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Vérifier que l'utilisateur est bien un laborantin (role_id = 4)
        if (Auth::user()->role_id != 4) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('gestionnaire.dashboard');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('medecin.dashboard');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('receptionniste.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $userId = Auth::id();

        // Statistiques de base
        $stats = [
            'examens_total' => Examen::where('laborantin_id', $userId)->count(),
            'examens_attente' => Examen::where('laborantin_id', $userId)->where('statut', 'en_attente')->count(),
            'examens_cours' => Examen::where('laborantin_id', $userId)->where('statut', 'en_cours')->count(),
            'examens_termines' => Examen::where('laborantin_id', $userId)->where('statut', 'termine')->count(),
            'taux_realisation' => Examen::where('laborantin_id', $userId)->count() > 0 
                ? round((Examen::where('laborantin_id', $userId)->where('statut', 'termine')->count() / Examen::where('laborantin_id', $userId)->count()) * 100, 2) 
                : 0,
        ];

        // Graphique d'évolution (6 derniers mois)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = ucfirst($date->locale('fr')->isoFormat('MMM YYYY'));
            $chartData[] = Examen::where('laborantin_id', $userId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Top 5 types d'examens
        $topExamensTypes = Examen::where('laborantin_id', $userId)
            ->select('type_examen', DB::raw('count(*) as total'))
            ->groupBy('type_examen')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Examens en attente
        $examensEnAttente = Examen::with(['patient'])
            ->where('laborantin_id', $userId)
            ->where('statut', 'en_attente')
            ->orderBy('date_examen', 'asc')
            ->limit(10)
            ->get();

        // Derniers examens réalisés
        $recentExamens = Examen::with(['patient'])
            ->where('laborantin_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Performance hebdomadaire
        $performanceHebdomadaire = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $jourMapping = ['Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7];
        
        foreach ($jours as $index => $jour) {
            $jourNumero = $jourMapping[$jour];
            $realises = Examen::where('laborantin_id', $userId)
                ->whereRaw('DAYOFWEEK(date_examen) = ?', [$jourNumero + 1])
                ->count();
            $performanceHebdomadaire[$index] = [
                'realises' => $realises,
                'prevus' => max($realises, 5)
            ];
        }

        // Évolution mensuelle avec variation
        $evolutionMensuelle = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = Examen::where('laborantin_id', $userId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $moisPrecedent = Examen::where('laborantin_id', $userId)
                ->whereYear('created_at', $date->copy()->subMonth()->year)
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