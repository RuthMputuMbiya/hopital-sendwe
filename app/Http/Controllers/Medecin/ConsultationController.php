<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Maladie;
use App\Models\Examen;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Liste des consultations du médecin
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        $query = Consultation::with(['patient', 'maladie'])
            ->where('medecin_id', Auth::id());
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_consultacion', 'LIKE', "%{$search}%")
                  ->orWhere('diagnostic', 'LIKE', "%{$search}%")
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
            $query->whereDate('date_consultation', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->whereDate('date_consultation', '<=', $dateFin);
        }
        
        $consultations = $query->orderBy('date_consultation', 'desc')->paginate(15);
        
        $stats = [
            'total' => Consultation::where('medecin_id', Auth::id())->count(),
            'aujourdhui' => Consultation::where('medecin_id', Auth::id())->whereDate('date_consultation', today())->count(),
            'en_cours' => Consultation::where('medecin_id', Auth::id())->where('statut', 'en_cours')->count(),
            'termines' => Consultation::where('medecin_id', Auth::id())->where('statut', 'termine')->count(),
            'ce_mois' => Consultation::where('medecin_id', Auth::id())->whereMonth('date_consultation', now()->month)->count(),
        ];
        
        return view('medecin.consultations.index', compact('consultations', 'stats', 'search', 'statut', 'dateDebut', 'dateFin'));
    }

    /**
     * Formulaire de création d'une consultation
     */
    public function create($patientId = null)
    {
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;
        
        return view('medecin.consultations.create', compact('patients', 'maladies', 'selectedPatient'));
    }

    /**
     * Enregistrer une nouvelle consultation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|exists:patients,id_patient',
            'sexe' => 'nullable|in:M,F',
            'date_naissance' => 'nullable|date|before:today',
            'telephone' => 'nullable|string|max:20',
            'motif' => 'required|string',
            'symptomes' => 'required|string',
            'diagnostic' => 'required|string',
            'id_maladie' => 'nullable|exists:maladies,id_maladie',
            'prescription' => 'nullable|string',
            'observations_medecin' => 'nullable|string',
            'tension_arterielle' => 'nullable|string|max:20',
            'poids' => 'nullable|numeric|min:0|max:300',
            'taille' => 'nullable|numeric|min:0|max:250',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'frequence_cardiaque' => 'nullable|integer|min:30|max:200',
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Mettre à jour les informations du patient si fournies
            $patient = Patient::find($request->id_patient);
            if ($patient) {
                $patientData = [];
                if ($request->sexe) $patientData['sexe'] = $request->sexe;
                if ($request->date_naissance) $patientData['date_naissance'] = $request->date_naissance;
                if ($request->telephone) $patientData['telephone'] = $request->telephone;
                if ($request->adresse_domicile) $patientData['adresse'] = $request->adresse_domicile;
                
                if (!empty($patientData)) {
                    $patient->update($patientData);
                }
            }

            $consultation = Consultation::create([
                'numero_consultacion' => $this->genererNumeroConsultation(),
                'date_consultation' => $request->date_consultation ?? today(),
                'heure_consultation' => now(),
                'motif' => $request->motif,
                'symptomes' => $request->symptomes,
                'diagnostic' => $request->diagnostic,
                'prescription' => $request->prescription,
                'observations_medecin' => $request->observations_medecin,
                'tension_arterielle' => $request->tension_arterielle,
                'poids' => $request->poids,
                'taille' => $request->taille,
                'temperature' => $request->temperature,
                'frequence_cardiaque' => $request->frequence_cardiaque,
                'id_patient' => $request->id_patient,
                'medecin_id' => Auth::id(),
                'id_maladie' => $request->id_maladie,
                'statut' => 'termine',
                'hospitalise' => $request->hospitalise ?? false,
                'pavillon' => $request->pavillon,
                'chambre' => $request->chambre,
                'lit' => $request->lit,
                'adresse_domicile' => $request->adresse_domicile,
                'moyen' => $request->moyen,
                'priorite' => $request->priorite ?? 'normal',
                'observations_cliniques' => $request->observations_cliniques,
            ]);

            // Calcul de l'IMC si taille et poids sont fournis
            if ($request->taille && $request->poids) {
                $tailleEnMetres = $request->taille / 100;
                $imc = round($request->poids / ($tailleEnMetres * $tailleEnMetres), 2);
                $consultation->imc = $imc;
                $consultation->save();
            }

            // Si des examens sont demandés, créer un bon d'analyse
            if ($request->examens && is_array($request->examens)) {
                $this->creerBonAnalyse($consultation, $request->examens);
            }

            DB::commit();

            return redirect()->route('medecin.consultations.show', $consultation->id_consultation)
                ->with('success', 'Consultation enregistrée avec succès ! Numéro: ' . $consultation->numero_consultacion);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'une consultation
     */
    public function show($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'maladie'])
            ->findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id() && !Auth::user()->estGestionnaire()) {
            abort(403);
        }
        
        $examens = Examen::where('id_patient', $consultation->id_patient)
            ->orderBy('date_examen', 'desc')
            ->limit(5)
            ->get();
        
        $consultationsPrecedentes = Consultation::where('id_patient', $consultation->id_patient)
            ->where('id_consultation', '!=', $id)
            ->orderBy('date_consultation', 'desc')
            ->limit(5)
            ->get();
        
        return view('medecin.consultations.show', compact('consultation', 'examens', 'consultationsPrecedentes'));
    }

    /**
     * Formulaire de modification d'une consultation
     */
    public function edit($id)
    {
        $consultation = Consultation::findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        
        return view('medecin.consultations.edit', compact('consultation', 'patients', 'maladies'));
    }

    /**
     * Mettre à jour une consultation
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'motif' => 'required|string',
            'symptomes' => 'required|string',
            'diagnostic' => 'required|string',
            'id_maladie' => 'nullable|exists:maladies,id_maladie',
            'prescription' => 'nullable|string',
            'observations_medecin' => 'nullable|string',
            'tension_arterielle' => 'nullable|string|max:20',
            'poids' => 'nullable|numeric|min:0|max:300',
            'taille' => 'nullable|numeric|min:0|max:250',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'frequence_cardiaque' => 'nullable|integer|min:30|max:200',
            'statut' => 'nullable|in:en_cours,termine,annule',
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
        
        if ($request->taille && $request->poids) {
            $tailleEnMetres = $request->taille / 100;
            $imc = round($request->poids / ($tailleEnMetres * $tailleEnMetres), 2);
            $consultation->imc = $imc;
            $consultation->save();
        }

        return redirect()->route('medecin.consultations.show', $consultation->id_consultation)
            ->with('success', 'Consultation modifiée avec succès');
    }

    /**
     * Supprimer une consultation
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        
        if ($consultation->medecin_id !== Auth::id()) {
            abort(403);
        }
        
        $consultation->delete();

        return redirect()->route('medecin.consultations.index')
            ->with('success', 'Consultation supprimée avec succès');
    }

    /**
     * Imprimer une consultation
     */
    public function imprimer($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'maladie'])
            ->findOrFail($id);
        
        return view('medecin.consultations.print', compact('consultation'));
    }

    /**
     * Ordonner des examens pour une consultation
     */
    public function ordonnerExamens(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'examens' => 'required|array|min:1',
            'examens.*.type' => 'required|string',
            'examens.*.date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            foreach ($request->examens as $examenData) {
                Examen::create([
                    'numero_examen' => $this->genererNumeroExamen(),
                    'type_examen' => $examenData['type'],
                    'date_examen' => $examenData['date'],
                    'heure_examen' => '08:00:00',
                    'id_patient' => $consultation->id_patient,
                    'consultation_id' => $consultation->id_consultation,
                    'id_maladie' => $consultation->id_maladie,
                    'laborantin_id' => null,
                    'medecin_id' => Auth::id(),
                    'statut' => 'en_attente',
                    'commentaires' => 'Prescrit par Dr. ' . (Auth::user()->nom_complet ?? Auth::user()->name),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('medecin.consultations.show', $consultation->id_consultation)
                ->with('success', count($request->examens) . ' examen(s) prescrit(s) avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la prescription: ' . $e->getMessage());
        }
    }

    /**
     * Créer un bon d'analyse à partir des examens demandés
     */
    private function creerBonAnalyse($consultation, $examens)
    {
        $bon = \App\Models\BonAnalyse::create([
            'numero_bon' => \App\Models\BonAnalyse::generateNumeroBon(),
            'id_patient' => $consultation->id_patient,
            'medecin_id' => Auth::id(),
            'hospitalise' => $consultation->hospitalise ?? false,
            'pavillon' => $consultation->pavillon,
            'chambre' => $consultation->chambre,
            'lit' => $consultation->lit,
            'adresse_domicile' => $consultation->adresse_domicile,
            'nom_medecin_demandeur' => Auth::user()->nom,
            'prenom_medecin_demandeur' => Auth::user()->prenom,
            'date_demande' => now(),
            'heure_demande' => now()->format('H:i'),
            'service_demandeur' => Auth::user()->specialite ?? 'Médecine Générale',
            'telephone_demandeur' => Auth::user()->telephone ?? 'N/A',
            'examens' => $examens,
            'priorite' => $consultation->priorite ?? 'normal',
            'statut' => 'en_attente',
            'observations_cliniques' => $consultation->observations_cliniques,
        ]);

        // Notifier les laborantins
        $laborantins = \App\Models\User::where('role_id', 4)->where('est_actif', true)->get();
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

    /**
     * Générer un numéro de consultation unique
     */
    private function genererNumeroConsultation()
    {
        $prefix = 'CONS';
        $year = date('Y');
        $month = date('m');
        
        $lastConsultation = Consultation::whereYear('created_at', $year)
            ->orderBy('id_consultation', 'desc')
            ->first();
        
        if ($lastConsultation && $lastConsultation->numero_consultacion) {
            $lastNumber = intval(substr($lastConsultation->numero_consultacion, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $year . $month . '-' . $newNumber;
    }

    /**
     * Générer un numéro d'examen unique
     */
    private function genererNumeroExamen()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastExamen = Examen::whereYear('created_at', $year)
            ->orderBy('id_examen', 'desc')
            ->first();
        
        if ($lastExamen && $lastExamen->numero_examen) {
            $lastNumber = intval(substr($lastExamen->numero_examen, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'EXAM-' . $year . $month . '-' . $newNumber;
    }
}