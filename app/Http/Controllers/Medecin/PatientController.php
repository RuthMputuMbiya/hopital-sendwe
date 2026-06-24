<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\HistoriqueMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Liste des patients du médecin
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut'); // tous, consultes, non_consultes
        
        // Récupérer les IDs des patients que le médecin a consultés
        $consultedPatientIds = Consultation::where('medecin_id', Auth::id())
            ->distinct()
            ->pluck('id_patient')
            ->toArray();
        
        $query = Patient::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('postnom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('numero_fiche', 'LIKE', "%{$search}%")
                  ->orWhere('telephone', 'LIKE', "%{$search}%");
            });
        }
        
        if ($statut == 'consultes') {
            $query->whereIn('id_patient', $consultedPatientIds);
        } elseif ($statut == 'non_consultes') {
            $query->whereNotIn('id_patient', $consultedPatientIds);
        }
        
        $patients = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Patient::count(),
            'consultes' => count($consultedPatientIds),
            'non_consultes' => Patient::count() - count($consultedPatientIds),
            'hommes' => Patient::where('sexe', 'M')->count(),
            'femmes' => Patient::where('sexe', 'F')->count(),
            'mois_actuel' => Patient::whereMonth('created_at', now()->month)->count(),
        ];
        
        return view('medecin.patients.index', compact('patients', 'stats', 'search', 'statut'));
    }

    /**
     * Afficher le dossier complet d'un patient
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Vérifier que le médecin a accès à ce patient
        $hasAccess = Consultation::where('medecin_id', Auth::id())
            ->where('id_patient', $id)
            ->exists();
        
        if (!$hasAccess && !Auth::user()->estGestionnaire()) {
            abort(403, 'Vous n\'avez pas accès au dossier de ce patient.');
        }
        
        // Toutes les consultations du patient
        $consultations = Consultation::with(['medecin', 'maladie'])
            ->where('id_patient', $id)
            ->orderBy('date_consultation', 'desc')
            ->paginate(10);
        
        // Tous les examens du patient
        $examens = Examen::with(['laborantin', 'maladie'])
            ->where('id_patient', $id)
            ->orderBy('date_examen', 'desc')
            ->paginate(10);
        
        // Dernière consultation
        $derniereConsultation = Consultation::where('id_patient', $id)
            ->orderBy('date_consultation', 'desc')
            ->first();
        
        // Prochain examen
        $prochainExamen = Examen::where('id_patient', $id)
            ->where('date_examen', '>=', today())
            ->orderBy('date_examen', 'asc')
            ->first();
        
        // Statistiques du patient
        $patientStats = [
            'total_consultations' => Consultation::where('id_patient', $id)->count(),
            'total_examens' => Examen::where('id_patient', $id)->count(),
            'derniere_visite' => $derniereConsultation ? $derniereConsultation->date_consultation->format('d/m/Y') : 'Aucune',
            'age' => $patient->age,
            'consultations_par_medecin' => Consultation::where('id_patient', $id)
                ->select('medecin_id', DB::raw('count(*) as total'))
                ->groupBy('medecin_id')
                ->with('medecin')
                ->get(),
        ];
        
        return view('medecin.patients.show', compact('patient', 'consultations', 'examens', 'derniereConsultation', 'prochainExamen', 'patientStats'));
    }

    /**
     * Historique médical complet du patient
     */
    public function historique($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Vérifier l'accès
        $hasAccess = Consultation::where('medecin_id', Auth::id())
            ->where('id_patient', $id)
            ->exists();
        
        if (!$hasAccess && !Auth::user()->estGestionnaire()) {
            abort(403);
        }
        
        // Historique des consultations
        $consultations = Consultation::with(['medecin', 'maladie'])
            ->where('id_patient', $id)
            ->orderBy('date_consultation', 'desc')
            ->get();
        
        // Historique des examens
        $examens = Examen::with(['laborantin', 'maladie'])
            ->where('id_patient', $id)
            ->orderBy('date_examen', 'desc')
            ->get();
        
        // Chronologie des événements médicaux
        $evenements = collect();
        
        foreach ($consultations as $consultation) {
            $evenements->push([
                'date' => $consultation->date_consultation,
                'type' => 'consultation',
                'titre' => 'Consultation médicale',
                'description' => $consultation->diagnostic,
                'medecin' => $consultation->medecin->nom_complet ?? 'N/A',
                'details' => $consultation
            ]);
        }
        
        foreach ($examens as $examen) {
            $evenements->push([
                'date' => $examen->date_examen,
                'type' => 'examen',
                'titre' => $examen->type_examen ?? 'Examen médical',
                'description' => $examen->resultat ?? 'En attente de résultat',
                'laborantin' => $examen->laborantin->nom_complet ?? 'N/A',
                'statut' => $examen->statut ?? 'en_attente',
                'details' => $examen
            ]);
        }
        
        $evenements = $evenements->sortByDesc('date');
        
        // Statistiques médicales
        $medicalStats = [
            'maladies_frequentes' => Consultation::where('id_patient', $id)
                ->whereNotNull('id_maladie')
                ->select('id_maladie', DB::raw('count(*) as total'))
                ->groupBy('id_maladie')
                ->with('maladie')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'consultations_par_annee' => Consultation::where('id_patient', $id)
                ->select(DB::raw('YEAR(date_consultation) as annee'), DB::raw('count(*) as total'))
                ->groupBy('annee')
                ->orderBy('annee', 'desc')
                ->get(),
            'examens_par_type' => Examen::where('id_patient', $id)
                ->select('type_examen', DB::raw('count(*) as total'))
                ->groupBy('type_examen')
                ->orderBy('total', 'desc')
                ->get(),
        ];
        
        return view('medecin.patients.historique', compact('patient', 'consultations', 'examens', 'evenements', 'medicalStats'));
    }

    /**
     * Rechercher des patients (AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        if (empty($search)) {
            return response()->json([]);
        }
        
        $patients = Patient::where('nom', 'LIKE', "%{$search}%")
            ->orWhere('postnom', 'LIKE', "%{$search}%")
            ->orWhere('prenom', 'LIKE', "%{$search}%")
            ->orWhere('numero_fiche', 'LIKE', "%{$search}%")
            ->orWhere('telephone', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['id_patient', 'nom', 'postnom', 'prenom', 'numero_fiche']);
        
        return response()->json($patients);
    }
}