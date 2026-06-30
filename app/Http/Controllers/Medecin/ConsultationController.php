<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Maladie;
use App\Models\Bilan;
use App\Models\BonAnalyse;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        $query = Consultation::with(['patient', 'maladie', 'infirmier'])
            ->where('medecin_id', Auth::id());
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_consultation', 'LIKE', "%{$search}%")
                  ->orWhere('diagnostic', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('nom', 'LIKE', "%{$search}%")
                         ->orWhere('postnom', 'LIKE', "%{$search}%")
                         ->orWhere('prenom', 'LIKE', "%{$search}%")
                         ->orWhere('numero_fiche', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($statut) {
            $query->where('statut_consultation', $statut);
        }
        
        if ($dateDebut) {
            $query->whereDate('date_consultation', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->whereDate('date_consultation', '<=', $dateFin);
        }
        
        $consultations = $query->orderBy('date_consultation', 'desc')->paginate(15);
        
        $stats = [
            'total' => Consultation::where('medecin_id', Auth::id())->count(),
            'aujourdhui' => Consultation::where('medecin_id', Auth::id())->whereDate('date_consultation', today())->count(),
            'en_attente_infirmier' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'en_attente_infirmier')->count(),
            'bilan_fait' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'bilan_fait')->count(),
            'en_consultation' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'en_consultation')->count(),
            'termines' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'termine')->count(),
            'annules' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'annule')->count(),
            'ce_mois' => Consultation::where('medecin_id', Auth::id())->whereMonth('date_consultation', now()->month)->count(),
        ];
        
        return view('medecin.consultations.index', compact('consultations', 'stats', 'search', 'statut', 'dateDebut', 'dateFin'));
    }

    public function create($patientId = null)
    {
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;
        $bilanInfirmier = null;
        $infirmier = null;
        
        if ($patientId) {
            // ✅ RECHERCHE DIRECTE DANS LA TABLE BILANS
            try {
                $bilanInfirmier = Bilan::where('id_patient', $patientId)
                    ->with('infirmier')
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {
                // Si la colonne n'existe pas, chercher via consultation
            }
            
            // Si pas trouvé, chercher via consultation
            if (!$bilanInfirmier) {
                try {
                    $bilanInfirmier = Bilan::whereHas('consultation', function($query) use ($patientId) {
                            $query->where('id_patient', $patientId);
                        })
                        ->with('infirmier')
                        ->orderBy('created_at', 'desc')
                        ->first();
                } catch (\Exception $e) {
                    // Ignorer
                }
            }
            
            // ✅ RÉCUPÉRER L'INFIRMIER
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
        
        return view('medecin.consultations.create', compact('patients', 'maladies', 'selectedPatient', 'bilanInfirmier', 'infirmier', 'examens'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|exists:patients,id_patient',
            'motif' => 'required|string|min:3',
            'symptomes' => 'required|string|min:3',
            'diagnostic' => 'required|string|min:3',
            'id_maladie' => 'nullable|exists:maladies,id_maladie',
            'prescription' => 'nullable|string',
            'observations_medecin' => 'nullable|string',
            'date_consultation' => 'nullable|date',
            'hospitalise' => 'nullable|boolean',
            'pavillon' => 'nullable|string|max:50',
            'chambre' => 'nullable|string|max:50',
            'lit' => 'nullable|string|max:50',
            'adresse_domicile' => 'nullable|string',
            'moyen' => 'nullable|string|max:50',
            'priorite' => 'nullable|in:normal,urgent,moyen',
            'examens' => 'nullable|array',
            'observations_cliniques' => 'nullable|string',
            'type_echantillon' => 'nullable|string',
            'autres_echantillon' => 'nullable|string',
            'service_demandeur' => 'nullable|string',
            'telephone_demandeur' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // ✅ RÉCUPÉRER LE BILAN INFIRMIER
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

            $consultation = Consultation::create([
                'numero_consultation' => $this->genererNumeroConsultation(),
                'date_consultation' => $request->date_consultation ?? today(),
                'heure_consultation' => now(),
                'motif' => $request->motif ?? ($bilanInfirmier->motif ?? null),
                'symptomes' => $request->symptomes ?? ($bilanInfirmier->symptomes ?? null),
                'diagnostic' => $request->diagnostic,
                'prescription' => $request->prescription,
                'observations_medecin' => $request->observations_medecin,
                'id_patient' => $request->id_patient,
                'medecin_id' => Auth::id(),
                'id_infirmier' => $bilanInfirmier->id_infirmier ?? null,
                'id_maladie' => $request->id_maladie,
                'statut' => 'en_cours',
                'statut_consultation' => 'en_consultation',
                'hospitalise' => $request->hospitalise ?? ($bilanInfirmier->hospitalise ?? false),
                'pavillon' => $request->pavillon ?? ($bilanInfirmier->pavillon ?? null),
                'chambre' => $request->chambre ?? ($bilanInfirmier->chambre ?? null),
                'lit' => $request->lit ?? ($bilanInfirmier->lit ?? null),
                'adresse_domicile' => $request->adresse_domicile,
                'moyen' => $request->moyen,
                'priorite' => $request->priorite ?? 'normal',
                'observations_cliniques' => $request->observations_cliniques,
            ]);

            if ($request->examens && is_array($request->examens)) {
                $this->creerBonAnalyse($consultation, $request->examens, $request);
            }

            DB::commit();

            return redirect()->route('medecin.consultations.show', $consultation->id_consultation)
                ->with('success', 'Consultation enregistrée avec succès ! Numéro: ' . $consultation->numero_consultation);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'infirmier', 'maladie', 'bilan', 'bonsAnalyse'])
            ->findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id() && !Auth::user()->estGestionnaire()) {
            abort(403);
        }
        
        return view('medecin.consultations.show', compact('consultation'));
    }

    public function edit($id)
    {
        $consultation = Consultation::with(['patient', 'infirmier', 'bilan'])
            ->findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        $bilanInfirmier = $consultation->bilan;
        $infirmier = $consultation->infirmier;
        
        return view('medecin.consultations.edit', compact('consultation', 'patients', 'maladies', 'bilanInfirmier', 'infirmier'));
    }

    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'motif' => 'required|string|min:3',
            'symptomes' => 'required|string|min:3',
            'diagnostic' => 'required|string|min:3',
            'id_maladie' => 'nullable|exists:maladies,id_maladie',
            'prescription' => 'nullable|string',
            'observations_medecin' => 'nullable|string',
            'statut_consultation' => 'nullable|in:en_attente_infirmier,bilan_fait,en_consultation,termine,annule',
            'hospitalise' => 'nullable|boolean',
            'pavillon' => 'nullable|string|max:50',
            'chambre' => 'nullable|string|max:50',
            'lit' => 'nullable|string|max:50',
            'adresse_domicile' => 'nullable|string',
            'moyen' => 'nullable|string|max:50',
            'priorite' => 'nullable|in:normal,urgent,moyen',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $consultation->update($request->all());

        return redirect()->route('medecin.consultations.show', $consultation->id_consultation)
            ->with('success', 'Consultation modifiée avec succès');
    }

    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        if ($consultation->statut_consultation === 'termine') {
            return back()->with('error', 'Impossible de supprimer une consultation terminée.');
        }
        
        $consultation->delete();

        return redirect()->route('medecin.consultations.index')
            ->with('success', 'Consultation supprimée avec succès');
    }

    public function imprimer($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'infirmier', 'maladie', 'bilan'])
            ->findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        return view('medecin.consultations.print', compact('consultation'));
    }

    private function creerBonAnalyse($consultation, $examens, $request)
    {
        $bilanInfirmier = null;
        try {
            $bilanInfirmier = Bilan::where('id_patient', $consultation->id_patient)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Exception $e) {}
        
        if (!$bilanInfirmier) {
            try {
                $bilanInfirmier = Bilan::whereHas('consultation', function($query) use ($consultation) {
                        $query->where('id_patient', $consultation->id_patient);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {}
        }

        $bon = BonAnalyse::create([
            'numero_bon' => BonAnalyse::generateNumeroBon(),
            'id_patient' => $consultation->id_patient,
            'id_consultation' => $consultation->id_consultation,
            'medecin_id' => Auth::id(),
            'id_infirmier' => $bilanInfirmier->id_infirmier ?? null,
            'bilan_infirmier_id' => $bilanInfirmier->id_bilan ?? null,
            'hospitalise' => $consultation->hospitalise ?? false,
            'pavillon' => $consultation->pavillon,
            'chambre' => $consultation->chambre,
            'lit' => $consultation->lit,
            'adresse_domicile' => $consultation->adresse_domicile,
            'nom_medecin_demandeur' => Auth::user()->nom,
            'prenom_medecin_demandeur' => Auth::user()->prenom,
            'date_demande' => now(),
            'heure_demande' => now()->format('H:i'),
            'service_demandeur' => $request->service_demandeur ?? Auth::user()->specialite ?? 'Médecine Générale',
            'telephone_demandeur' => $request->telephone_demandeur ?? Auth::user()->telephone ?? 'N/A',
            'type_echantillon' => $request->type_echantillon,
            'autres_echantillon' => $request->autres_echantillon,
            'examens' => $examens,
            'priorite' => $consultation->priorite ?? 'normal',
            'statut' => 'en_attente',
            'observations_cliniques' => $request->observations_cliniques ?? $consultation->observations_medecin,
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
                'message' => "Bon N° {$bon->numero_bon} - Patient: {$consultation->patient->nom_complet}",
                'type' => 'info',
                'user_id' => $laborantin->id_user,
                'lien' => route('laborantin.bons-analyse.show', $bon->id_bon),
                'est_lue' => false,
            ]);
        }

        return $bon;
    }

    private function genererNumeroConsultation()
    {
        $prefix = 'CONS';
        $year = date('Y');
        $month = date('m');
        
        $lastConsultation = Consultation::whereYear('created_at', $year)
            ->orderBy('id_consultation', 'desc')
            ->first();
        
        if ($lastConsultation && $lastConsultation->numero_consultation) {
            $lastNumber = intval(substr($lastConsultation->numero_consultation, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $year . $month . '-' . $newNumber;
    }

    public function statistiques()
    {
        $stats = [
            'total' => Consultation::where('medecin_id', Auth::id())->count(),
            'aujourdhui' => Consultation::where('medecin_id', Auth::id())->whereDate('date_consultation', today())->count(),
            'cette_semaine' => Consultation::where('medecin_id', Auth::id())->whereBetween('date_consultation', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'ce_mois' => Consultation::where('medecin_id', Auth::id())->whereMonth('date_consultation', now()->month)->count(),
            'en_attente' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'en_attente_infirmier')->count(),
            'en_cours' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'en_consultation')->count(),
            'termines' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'termine')->count(),
            'annules' => Consultation::where('medecin_id', Auth::id())->where('statut_consultation', 'annule')->count(),
            'avec_bilan' => Consultation::where('medecin_id', Auth::id())->whereNotNull('id_infirmier')->count(),
            'sans_bilan' => Consultation::where('medecin_id', Auth::id())->whereNull('id_infirmier')->count(),
        ];
        
        return view('medecin.consultations.statistiques', compact('stats'));
    }
}