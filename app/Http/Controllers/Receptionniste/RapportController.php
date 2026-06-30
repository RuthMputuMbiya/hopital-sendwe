<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RapportController extends Controller
{
    /**
     * Page principale des rapports
     */
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_consultations' => Consultation::count(),
            'total_examens' => Examen::count(),
            'patients_mois' => Patient::whereMonth('created_at', now()->month)->count(),
            'consultations_mois' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'examens_mois' => Examen::whereMonth('date_examen', now()->month)->count(),
        ];
        
        // ✅ CORRIGÉ : Suppression de 'created_by' qui n'existe pas
        $monActivite = [
            'patients_enregistres' => Patient::count(), // Total des patients
            'patients_semaine' => Patient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'patients_mois' => Patient::whereMonth('created_at', now()->month)->count(),
        ];
        
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = ucfirst($date->locale('fr')->isoFormat('ddd D MMM'));
            $chartData[] = Patient::whereDate('created_at', $date)->count();
        }
        
        $topMedecins = User::where('role_id', 2)
            ->withCount('consultationsEffectuees')
            ->orderBy('consultations_effectuees_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('receptionniste.rapports.index', compact('stats', 'monActivite', 'chartLabels', 'chartData', 'topMedecins'));
    }

    /**
     * Historique complet des activités
     */
    public function historique(Request $request)
    {
        $search = $request->get('search');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        // ✅ CORRIGÉ : Suppression de 'created_by'
        $query = Patient::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('numero_fiche', 'LIKE', "%{$search}%")
                  ->orWhere('telephone', 'LIKE', "%{$search}%");
            });
        }
        
        if ($dateDebut) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin) {
            $query->whereDate('created_at', '<=', $dateFin);
        }
        
        $historiques = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // ✅ CORRIGÉ : Suppression de 'created_by'
        $stats = [
            'total' => Patient::count(),
            'aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'semaine' => Patient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'mois' => Patient::whereMonth('created_at', now()->month)->count(),
        ];
        
        return view('receptionniste.rapports.historique', compact('historiques', 'stats', 'search', 'dateDebut', 'dateFin'));
    }

    /**
     * Formulaire pour générer un rapport
     */
    public function genererRapport(Request $request)
    {
        $typeRapport = $request->get('type_rapport', 'general');
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        return view('receptionniste.rapports.generer', compact('typeRapport', 'periode', 'dateDebut', 'dateFin'));
    }

    /**
     * Aperçu du rapport (appelé par le formulaire)
     */
    public function apercu(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $typeRapport = $request->get('type_rapport', 'general');
        
        // Définir les dates
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
                case 'mois':
                    $debut = Carbon::now()->startOfMonth()->startOfDay();
                    $fin = Carbon::now()->endOfMonth()->endOfDay();
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
        
        // Construire le rapport
        $rapport = [
            'periode' => [
                'debut' => $debut,
                'fin' => $fin,
                'libelle' => "du " . $debut->format('d/m/Y') . " au " . $fin->format('d/m/Y')
            ],
            'generated_by' => auth()->user(),
            'generated_at' => now(),
            'type' => $typeRapport,
        ];
        
        // Ajouter les données selon le type
        if ($typeRapport == 'general' || $typeRapport == 'patients') {
            $rapport['patients'] = Patient::whereBetween('created_at', [$debut, $fin])->orderBy('created_at', 'desc')->get();
            $rapport['total_patients'] = Patient::whereBetween('created_at', [$debut, $fin])->count();
            $rapport['patients_par_sexe'] = [
                'hommes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'M')->count(),
                'femmes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'F')->count(),
            ];
        }
        
        if ($typeRapport == 'general' || $typeRapport == 'consultations') {
            $rapport['consultations'] = Consultation::with(['patient', 'medecin'])
                ->whereBetween('date_consultation', [$debut, $fin])
                ->orderBy('date_consultation', 'desc')
                ->get();
            $rapport['total_consultations'] = Consultation::whereBetween('date_consultation', [$debut, $fin])->count();
            $rapport['consultations_par_medecin'] = Consultation::whereBetween('date_consultation', [$debut, $fin])
                ->select('medecin_id', DB::raw('count(*) as total'))
                ->groupBy('medecin_id')
                ->with('medecin')
                ->get();
        }
        
        if ($typeRapport == 'general' || $typeRapport == 'examens') {
            $rapport['examens'] = Examen::with(['patient'])
                ->whereBetween('date_examen', [$debut, $fin])
                ->orderBy('date_examen', 'desc')
                ->get();
            $rapport['total_examens'] = Examen::whereBetween('date_examen', [$debut, $fin])->count();
            $rapport['examens_par_type'] = Examen::whereBetween('date_examen', [$debut, $fin])
                ->select('type_examen', DB::raw('count(*) as total'))
                ->groupBy('type_examen')
                ->orderBy('total', 'desc')
                ->get();
        }
        
        // ✅ CORRIGÉ : Suppression de 'created_by'
        $rapport['mon_activite'] = Patient::whereBetween('created_at', [$debut, $fin])->count();
        
        return view('receptionniste.rapports.apercu', compact('rapport'));
    }

    /**
     * Exporter le rapport en PDF (Version impression navigateur)
     */
    public function exportPDF(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $typeRapport = $request->get('type_rapport', 'general');
        
        // Définir les dates
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
                case 'mois':
                    $debut = Carbon::now()->startOfMonth()->startOfDay();
                    $fin = Carbon::now()->endOfMonth()->endOfDay();
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
        
        $rapport = [
            'periode' => [
                'debut' => $debut,
                'fin' => $fin,
                'libelle' => "du " . $debut->format('d/m/Y') . " au " . $fin->format('d/m/Y')
            ],
            'generated_by' => auth()->user(),
            'generated_at' => now(),
            'type' => $typeRapport,
        ];
        
        if ($typeRapport == 'general' || $typeRapport == 'patients') {
            $rapport['patients'] = Patient::whereBetween('created_at', [$debut, $fin])->orderBy('created_at', 'desc')->get();
            $rapport['total_patients'] = Patient::whereBetween('created_at', [$debut, $fin])->count();
            $rapport['patients_par_sexe'] = [
                'hommes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'M')->count(),
                'femmes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'F')->count(),
            ];
        }
        
        if ($typeRapport == 'general' || $typeRapport == 'consultations') {
            $rapport['consultations'] = Consultation::with(['patient', 'medecin'])
                ->whereBetween('date_consultation', [$debut, $fin])
                ->orderBy('date_consultation', 'desc')
                ->get();
            $rapport['total_consultations'] = Consultation::whereBetween('date_consultation', [$debut, $fin])->count();
        }
        
        if ($typeRapport == 'general' || $typeRapport == 'examens') {
            $rapport['examens'] = Examen::with(['patient'])
                ->whereBetween('date_examen', [$debut, $fin])
                ->orderBy('date_examen', 'desc')
                ->get();
            $rapport['total_examens'] = Examen::whereBetween('date_examen', [$debut, $fin])->count();
        }
        
        // ✅ CORRIGÉ : Suppression de 'created_by'
        $rapport['mon_activite'] = Patient::whereBetween('created_at', [$debut, $fin])->count();
        
        // Retourner la vue pour impression
        return view('receptionniste.rapports.export-pdf', compact('rapport'));
    }
}