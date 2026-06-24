<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BonAnalyseController extends Controller
{
    /**
     * Liste des bons d'analyse
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        
        $query = BonAnalyse::with(['patient', 'medecin'])
            ->where('medecin_id', Auth::id());
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_bon', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('nom', 'LIKE', "%{$search}%")
                         ->orWhere('postnom', 'LIKE', "%{$search}%")
                         ->orWhere('prenom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($statut) {
            $query->where('statut', $statut);
        }
        
        $bons = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => BonAnalyse::where('medecin_id', Auth::id())->count(),
            'en_attente' => BonAnalyse::where('medecin_id', Auth::id())->where('statut', 'en_attente')->count(),
            'en_cours' => BonAnalyse::where('medecin_id', Auth::id())->where('statut', 'en_cours')->count(),
            'termines' => BonAnalyse::where('medecin_id', Auth::id())->where('statut', 'termine')->count(),
        ];
        
        return view('medecin.bons_analyse.index', compact('bons', 'stats', 'search', 'statut'));
    }

    /**
     * Formulaire de création d'un bon d'analyse
     */
    public function create($patientId = null)
    {
        $patients = Patient::orderBy('nom')->get();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;
        
        $examens = [
            'parasitologie' => [
                'culot_urines' => 'Culot Urines',
                'sucre_urines' => 'Sucre Urines',
                'albuminurie' => 'Albuminurie',
                'ph_urines' => 'pH Urines',
                'acetone_urines' => 'Acétone Urines',
                'selles_frais' => 'Selles à frais',
                'h_pylori' => 'H. Pylori',
                'tdr_malaria' => 'TDR Malaria',
                'goutte_epaisse' => 'Goutte Épaisse',
                'goutte_fraiche' => 'Goutte Fraîche',
                'trypanosome' => 'Recherche Trypanosome'
            ],
            'hematologie' => [
                'gs' => 'GS',
                'rh' => 'Rh',
                'hct' => 'Hct',
                'gb' => 'GB',
                'gr' => 'GR',
                'fl' => 'FL',
                'vs' => 'VS',
                'plt' => 'PLT',
                'reticulocytes' => 'Réticulocytes',
                'vgm' => 'VGM',
                'ccmh' => 'CCMH',
                'tcmh' => 'TCMH'
            ],
            'microbiologie' => [
                'gram_ponctions' => 'Gram-Ponctions',
                'cytologie' => 'Cytologie',
                'rivalta' => 'Rivalta',
                'ziehl' => 'Ziehl',
                'elements_gram' => 'Éléments Gram',
                'gram' => 'Gram'
            ],
            'biochimie' => [
                'glycemie' => 'Glycémie',
                'uree' => 'Urée',
                'creatinine' => 'Créatinine',
                'cholesterol_total' => 'Cholestérol Total',
                'triglycerides' => 'Triglycérides',
                'hdl' => 'HDL-Cholestérol',
                'ldl' => 'LDL-Cholestérol',
                'acide_urique' => 'Acide urique'
            ],
            'immuno_serologie' => [
                'aghbs' => 'AgHBS',
                'achcv' => 'AcHCV',
                'rpr_vdrl' => 'RPR/VDRL',
                'aslo' => 'ASLO',
                'crp' => 'CRP'
            ]
        ];
        
        return view('medecin.bons_analyse.create', compact('patients', 'selectedPatient', 'examens'));
    }

    /**
     * Enregistrer un bon d'analyse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|exists:patients,id_patient',
            'hospitalise' => 'nullable|boolean',
            'pavillon' => 'nullable|string|max:50',
            'chambre' => 'nullable|string|max:50',
            'lit' => 'nullable|string|max:50',
            'adresse_domicile' => 'nullable|string',
            'date_demande' => 'required|date',
            'heure_demande' => 'required',
            'service_demandeur' => 'required|string|max:100',
            'telephone_demandeur' => 'required|string|max:20',
            'type_echantillon' => 'nullable|in:sang,selles,urines,lcr,crachat,autres',
            'autres_echantillon' => 'nullable|string|max:100',
            'priorite' => 'nullable|in:normal,urgent',
            'examens' => 'nullable|array',
            'observations_cliniques' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $patient = Patient::find($request->id_patient);
            $medecin = Auth::user();

            $bon = BonAnalyse::create([
                'numero_bon' => BonAnalyse::generateNumeroBon(),
                'id_patient' => $request->id_patient,
                'medecin_id' => Auth::id(),
                'hospitalise' => $request->hospitalise ?? false,
                'pavillon' => $request->pavillon,
                'chambre' => $request->chambre,
                'lit' => $request->lit,
                'adresse_domicile' => $request->adresse_domicile,
                'nom_medecin_demandeur' => $medecin->nom,
                'prenom_medecin_demandeur' => $medecin->prenom,
                'date_demande' => $request->date_demande,
                'heure_demande' => $request->heure_demande,
                'service_demandeur' => $request->service_demandeur,
                'telephone_demandeur' => $request->telephone_demandeur,
                'type_echantillon' => $request->type_echantillon,
                'autres_echantillon' => $request->autres_echantillon,
                'examens' => $request->examens,
                'priorite' => $request->priorite ?? 'normal',
                'statut' => 'en_attente',
                'observations_cliniques' => $request->observations_cliniques,
            ]);

            // Notifier les laborantins
            $laborantins = User::where('role_id', 4)->where('est_actif', true)->get();
            foreach ($laborantins as $laborantin) {
                \App\Models\Notification::create([
                    'titre' => '📋 Nouveau bon d\'analyse',
                    'message' => "Bon N° {$bon->numero_bon} - Patient: {$patient->nom_complet}",
                    'type' => 'info',
                    'user_id' => $laborantin->id_user,
                    'lien' => route('laborantin.bons-analyse.show', $bon->id_bon),
                    'est_lue' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('medecin.bons-analyse.show', $bon->id_bon)
                ->with('success', 'Bon d\'analyse créé avec succès ! N°: ' . $bon->numero_bon);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher un bon d'analyse
     */
    public function show($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'laborantin'])->findOrFail($id);
        return view('medecin.bons_analyse.show', compact('bon'));
    }

    /**
     * Imprimer un bon d'analyse spécifique (par ID)
     */
    public function print($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin'])->findOrFail($id);
        return view('medecin.bons_analyse.print', compact('bon'));
    }

    /**
     * ⭐ Imprimer le dernier bon d'analyse d'un patient spécifique (par ID patient)
     * Cette méthode est utilisée depuis la liste des consultations
     */
    public function printByPatient($patientId)
    {
        // Récupérer le dernier bon d'analyse du patient
        $bon = BonAnalyse::with(['patient', 'medecin'])
            ->where('id_patient', $patientId)
            ->where('medecin_id', Auth::id())
            ->latest()
            ->first();
        
        // Si aucun bon n'existe, en créer un vide avec les infos du patient
        if (!$bon) {
            $patient = Patient::findOrFail($patientId);
            $medecin = Auth::user();
            
            $bon = BonAnalyse::create([
                'numero_bon' => BonAnalyse::generateNumeroBon(),
                'id_patient' => $patientId,
                'medecin_id' => Auth::id(),
                'hospitalise' => false,
                'nom_medecin_demandeur' => $medecin->nom,
                'prenom_medecin_demandeur' => $medecin->prenom,
                'date_demande' => now(),
                'heure_demande' => now()->format('H:i'),
                'service_demandeur' => $medecin->specialite ?? 'Médecine Générale',
                'telephone_demandeur' => $medecin->telephone ?? 'N/A',
                'statut' => 'en_attente',
                'priorite' => 'normal',
                'examens' => [],
                'observations_cliniques' => null,
            ]);
        }
        
        return view('medecin.bons_analyse.print', compact('bon'));
    }
}