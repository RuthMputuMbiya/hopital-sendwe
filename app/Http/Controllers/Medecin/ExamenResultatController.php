<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamenResultatController extends Controller
{
    /**
     * Liste des résultats d'examens
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        // Récupérer les IDs des patients du médecin
        $patientIds = Consultation::where('medecin_id', Auth::id())
            ->distinct()
            ->pluck('id_patient');
        
        $query = Examen::with(['patient', 'laborantin', 'maladie'])
            ->whereIn('id_patient', $patientIds);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_examen', 'LIKE', "%{$search}%")
                  ->orWhere('type_examen', 'LIKE', "%{$search}%")
                  ->orWhere('resultat', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('nom', 'LIKE', "%{$search}%")
                         ->orWhere('prenom', 'LIKE', "%{$search}%")
                         ->orWhere('numero_fiche', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($statut) {
            $query->where('statut', $statut);
        }
        
        if ($dateDebut) {
            $query->whereDate('date_examen', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->whereDate('date_examen', '<=', $dateFin);
        }
        
        $examens = $query->orderBy('date_examen', 'desc')->paginate(15);
        
        $stats = [
            'total' => $query->count(),
            'termines' => Examen::whereIn('id_patient', $patientIds)->where('statut', 'termine')->count(),
            'en_attente' => Examen::whereIn('id_patient', $patientIds)->where('statut', 'en_attente')->count(),
            'ce_mois' => Examen::whereIn('id_patient', $patientIds)
                ->whereMonth('date_examen', now()->month)
                ->count(),
        ];
        
        return view('medecin.examens.index', compact('examens', 'stats', 'search', 'statut', 'dateDebut', 'dateFin'));
    }

    /**
     * Afficher les détails d'un examen
     */
    public function show($id)
    {
        $examen = Examen::with(['patient', 'laborantin', 'maladie'])
            ->findOrFail($id);
        
        // Vérifier que le médecin a accès à ce patient
        $hasAccess = Consultation::where('medecin_id', Auth::id())
            ->where('id_patient', $examen->id_patient)
            ->exists();
        
        if (!$hasAccess && !Auth::user()->estGestionnaire()) {
            abort(403, 'Vous n\'avez pas accès à cet examen.');
        }
        
        // Autres examens du même patient
        $autresExamens = Examen::where('id_patient', $examen->id_patient)
            ->where('id_examen', '!=', $id)
            ->orderBy('date_examen', 'desc')
            ->limit(5)
            ->get();
        
        // Dernière consultation du patient
        $derniereConsultation = Consultation::where('id_patient', $examen->id_patient)
            ->orderBy('date_consultation', 'desc')
            ->first();
        
        return view('medecin.examens.show', compact('examen', 'autresExamens', 'derniereConsultation'));
    }

    /**
     * Imprimer un résultat d'examen
     */
    public function imprimer($id)
    {
        $examen = Examen::with(['patient', 'laborantin', 'maladie'])
            ->findOrFail($id);
        
        return view('medecin.examens.print', compact('examen'));
    }
}