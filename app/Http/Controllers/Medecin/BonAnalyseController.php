<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use App\Models\Patient;
use App\Models\Bilan;
use App\Models\Maladie;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BonAnalyseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        
        $query = BonAnalyse::with(['patient', 'medecin', 'infirmier'])
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
            'preleves' => BonAnalyse::where('medecin_id', Auth::id())->where('statut', 'preleve')->count(),
            'analyse' => BonAnalyse::where('medecin_id', Auth::id())->where('statut', 'analyse')->count(),
        ];
        
        return view('medecin.bons_analyse.index', compact('bons', 'stats', 'search', 'statut'));
    }

    public function create($patientId = null)
    {
        $patients = Patient::orderBy('nom')->get();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;
        $bilanInfirmier = null;
        $infirmier = null;
        $maladies = Maladie::orderBy('nom_maladie')->get();
        
        if ($patientId) {
            try {
                $bilanInfirmier = Bilan::where('id_patient', $patientId)
                    ->with('infirmier')
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {}
            
            if (!$bilanInfirmier) {
                try {
                    $bilanInfirmier = Bilan::whereHas('consultation', function($query) use ($patientId) {
                            $query->where('id_patient', $patientId);
                        })
                        ->with('infirmier')
                        ->orderBy('created_at', 'desc')
                        ->first();
                } catch (\Exception $e) {}
            }
            
            if ($bilanInfirmier) {
                if ($bilanInfirmier->infirmier) {
                    $infirmier = $bilanInfirmier->infirmier;
                } else if ($bilanInfirmier->id_infirmier) {
                    $infirmier = User::find($bilanInfirmier->id_infirmier);
                }
            }
        }
        
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
                'gs' => 'GS', 'rh' => 'Rh', 'hct' => 'Hct', 'gb' => 'GB',
                'gr' => 'GR', 'fl' => 'FL', 'vs' => 'VS', 'plt' => 'PLT',
                'reticulocytes' => 'Réticulocytes', 'vgm' => 'VGM',
                'ccmh' => 'CCMH', 'tcmh' => 'TCMH'
            ],
            'microbiologie' => [
                'gram_ponctions' => 'Gram-Ponctions', 'cytologie' => 'Cytologie',
                'rivalta' => 'Rivalta', 'ziehl' => 'Ziehl',
                'elements_gram' => 'Éléments Gram', 'gram' => 'Gram'
            ],
            'biochimie' => [
                'glycemie' => 'Glycémie', 'uree' => 'Urée',
                'creatinine' => 'Créatinine', 'cholesterol_total' => 'Cholestérol Total',
                'triglycerides' => 'Triglycérides', 'hdl' => 'HDL-Cholestérol',
                'ldl' => 'LDL-Cholestérol', 'acide_urique' => 'Acide urique'
            ],
            'immuno_serologie' => [
                'aghbs' => 'AgHBS', 'achcv' => 'AcHCV',
                'rpr_vdrl' => 'RPR/VDRL', 'aslo' => 'ASLO', 'crp' => 'CRP'
            ]
        ];
        
        return view('medecin.bons_analyse.create', compact('patients', 'selectedPatient', 'examens', 'bilanInfirmier', 'infirmier', 'maladies'));
    }

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
            'priorite' => 'nullable|in:normal,urgent,moyen', // ✅ AJOUTÉ 'moyen'
            'examens' => 'nullable|array',
            'observations_cliniques' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $patient = Patient::find($request->id_patient);
            $medecin = Auth::user();
            
            $bilanInfirmier = null;
            try {
                $bilanInfirmier = Bilan::where('id_patient', $request->id_patient)
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {}
            
            if (!$bilanInfirmier) {
                try {
                    $bilanInfirmier = Bilan::whereHas('consultation', function($query) use ($request) {
                            $query->where('id_patient', $request->id_patient);
                        })
                        ->orderBy('created_at', 'desc')
                        ->first();
                } catch (\Exception $e) {}
            }

            $bon = BonAnalyse::create([
                'numero_bon' => BonAnalyse::generateNumeroBon(),
                'id_patient' => $request->id_patient,
                'id_consultation' => $request->id_consultation,
                'medecin_id' => Auth::id(),
                'id_infirmier' => $bilanInfirmier->id_infirmier ?? null,
                'bilan_infirmier_id' => $bilanInfirmier->id_bilan ?? null,
                'laborantin_id' => null,
                'hospitalise' => $request->hospitalise ?? ($bilanInfirmier->hospitalise ?? false),
                'pavillon' => $request->pavillon ?? ($bilanInfirmier->pavillon ?? null),
                'chambre' => $request->chambre ?? ($bilanInfirmier->chambre ?? null),
                'lit' => $request->lit ?? ($bilanInfirmier->lit ?? null),
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
                'priorite' => $request->priorite ?? 'normal', // ✅ normal, urgent ou moyen
                'statut' => 'en_attente',
                'observations_cliniques' => $request->observations_cliniques,
                'motif_infirmier' => $bilanInfirmier->motif ?? null,
                'symptomes_infirmier' => $bilanInfirmier->symptomes ?? null,
                'observations_infirmier' => $bilanInfirmier->observations ?? null,
                'allergies_infirmier' => $bilanInfirmier->allergies ?? null,
                'traitements_infirmier' => $bilanInfirmier->traitements_en_cours ?? null,
                'poids_infirmier' => $bilanInfirmier->poids ?? null,
                'taille_infirmier' => $bilanInfirmier->taille ?? null,
                'temperature_infirmier' => $bilanInfirmier->temperature ?? null,
                'tension_arterielle_infirmier' => $bilanInfirmier->tension_arterielle ?? null,
                'frequence_cardiaque_infirmier' => $bilanInfirmier->frequence_cardiaque ?? null,
                'frequence_respiratoire_infirmier' => $bilanInfirmier->frequence_respiratoire ?? null,
                'spo2_infirmier' => $bilanInfirmier->spo2 ?? null,
            ]);

            $laborantins = User::where('role_id', 4)->where('est_actif', true)->get();
            foreach ($laborantins as $laborantin) {
                Notification::create([
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

    public function show($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'laborantin', 'bilanInfirmier'])
            ->findOrFail($id);
        
        if ($bon->medecin_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à ce bon d\'analyse.');
        }
        
        return view('medecin.bons_analyse.show', compact('bon'));
    }

    public function print($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'laborantin', 'bilanInfirmier'])
            ->findOrFail($id);
        
        if ($bon->medecin_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à ce bon d\'analyse.');
        }
        
        return view('medecin.bons_analyse.print', compact('bon'));
    }

    public function printByPatient($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'bilanInfirmier'])
            ->where('id_patient', $patientId)
            ->where('medecin_id', Auth::id())
            ->latest()
            ->first();
        
        $bilanInfirmier = null;
        try {
            $bilanInfirmier = Bilan::where('id_patient', $patientId)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Exception $e) {}
        
        if (!$bilanInfirmier) {
            try {
                $bilanInfirmier = Bilan::whereHas('consultation', function($query) use ($patientId) {
                        $query->where('id_patient', $patientId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {}
        }
        
        if (!$bon) {
            $medecin = Auth::user();
            $bon = BonAnalyse::create([
                'numero_bon' => BonAnalyse::generateNumeroBon(),
                'id_patient' => $patientId,
                'medecin_id' => Auth::id(),
                'id_infirmier' => $bilanInfirmier->id_infirmier ?? null,
                'bilan_infirmier_id' => $bilanInfirmier->id_bilan ?? null,
                'hospitalise' => $bilanInfirmier->hospitalise ?? false,
                'pavillon' => $bilanInfirmier->pavillon ?? null,
                'chambre' => $bilanInfirmier->chambre ?? null,
                'lit' => $bilanInfirmier->lit ?? null,
                'nom_medecin_demandeur' => $medecin->nom,
                'prenom_medecin_demandeur' => $medecin->prenom,
                'date_demande' => now(),
                'heure_demande' => now()->format('H:i'),
                'service_demandeur' => $medecin->specialite ?? 'Médecine Générale',
                'telephone_demandeur' => $medecin->telephone ?? 'N/A',
                'statut' => 'en_attente',
                'priorite' => 'normal',
                'examens' => [],
                'motif_infirmier' => $bilanInfirmier->motif ?? null,
                'symptomes_infirmier' => $bilanInfirmier->symptomes ?? null,
                'observations_infirmier' => $bilanInfirmier->observations ?? null,
                'allergies_infirmier' => $bilanInfirmier->allergies ?? null,
                'traitements_infirmier' => $bilanInfirmier->traitements_en_cours ?? null,
                'poids_infirmier' => $bilanInfirmier->poids ?? null,
                'taille_infirmier' => $bilanInfirmier->taille ?? null,
                'temperature_infirmier' => $bilanInfirmier->temperature ?? null,
                'tension_arterielle_infirmier' => $bilanInfirmier->tension_arterielle ?? null,
                'frequence_cardiaque_infirmier' => $bilanInfirmier->frequence_cardiaque ?? null,
                'frequence_respiratoire_infirmier' => $bilanInfirmier->frequence_respiratoire ?? null,
                'spo2_infirmier' => $bilanInfirmier->spo2 ?? null,
            ]);
        }
        
        return view('medecin.bons_analyse.print', compact('bon', 'bilanInfirmier'));
    }

    public function edit($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier'])
            ->findOrFail($id);
        
        if ($bon->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        
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
                'gs' => 'GS', 'rh' => 'Rh', 'hct' => 'Hct', 'gb' => 'GB',
                'gr' => 'GR', 'fl' => 'FL', 'vs' => 'VS', 'plt' => 'PLT',
                'reticulocytes' => 'Réticulocytes', 'vgm' => 'VGM',
                'ccmh' => 'CCMH', 'tcmh' => 'TCMH'
            ],
            'microbiologie' => [
                'gram_ponctions' => 'Gram-Ponctions', 'cytologie' => 'Cytologie',
                'rivalta' => 'Rivalta', 'ziehl' => 'Ziehl',
                'elements_gram' => 'Éléments Gram', 'gram' => 'Gram'
            ],
            'biochimie' => [
                'glycemie' => 'Glycémie', 'uree' => 'Urée',
                'creatinine' => 'Créatinine', 'cholesterol_total' => 'Cholestérol Total',
                'triglycerides' => 'Triglycérides', 'hdl' => 'HDL-Cholestérol',
                'ldl' => 'LDL-Cholestérol', 'acide_urique' => 'Acide urique'
            ],
            'immuno_serologie' => [
                'aghbs' => 'AgHBS', 'achcv' => 'AcHCV',
                'rpr_vdrl' => 'RPR/VDRL', 'aslo' => 'ASLO', 'crp' => 'CRP'
            ]
        ];
        
        return view('medecin.bons_analyse.edit', compact('bon', 'patients', 'examens', 'maladies'));
    }

    public function update(Request $request, $id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        if ($bon->medecin_id !== Auth::id()) {
            abort(403);
        }

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
            'priorite' => 'nullable|in:normal,urgent,moyen', // ✅ AJOUTÉ 'moyen'
            'examens' => 'nullable|array',
            'observations_cliniques' => 'nullable|string'
        ]);

        $bon->update($request->all());

        return redirect()->route('medecin.bons-analyse.show', $bon->id_bon)
            ->with('success', 'Bon d\'analyse modifié avec succès');
    }

    public function destroy($id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        if ($bon->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        $bon->delete();

        return redirect()->route('medecin.bons-analyse.index')
            ->with('success', 'Bon d\'analyse supprimé avec succès');
    }
}