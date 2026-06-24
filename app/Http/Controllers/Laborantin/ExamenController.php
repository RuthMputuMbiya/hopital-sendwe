<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Patient;
use App\Models\Maladie;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamenController extends Controller
{
    /**
     * Liste des examens
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        
        $query = Examen::with(['patient', 'maladie', 'laborantin'])
            ->where('laborantin_id', Auth::id());
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_examen', 'LIKE', "%{$search}%")
                  ->orWhere('type_examen', 'LIKE', "%{$search}%")
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
        
        $examens = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => Examen::where('laborantin_id', Auth::id())->count(),
            'attente' => Examen::where('laborantin_id', Auth::id())->where('statut', 'en_attente')->count(),
            'termines' => Examen::where('laborantin_id', Auth::id())->where('statut', 'termine')->count(),
            'annules' => Examen::where('laborantin_id', Auth::id())->where('statut', 'annule')->count(),
        ];
        
        return view('laborantin.examens.index', compact('examens', 'stats', 'search', 'statut'));
    }

    /**
     * Examens en attente
     */
    public function enAttente()
    {
        $examens = Examen::with(['patient', 'maladie'])
            ->where('laborantin_id', Auth::id())
            ->where('statut', 'en_attente')
            ->orderBy('date_examen', 'asc')
            ->paginate(15);
        
        $stats = [
            'total_attente' => $examens->total(),
        ];
        
        return view('laborantin.examens.attente', compact('examens', 'stats'));
    }

    /**
     * Formulaire de création d'examen
     */
    public function create($patientId = null)
    {
        $patients = Patient::orderBy('nom')->get();
        $maladies = Maladie::orderBy('nom_maladie')->get();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;
        
        return view('laborantin.examens.create', compact('patients', 'maladies', 'selectedPatient'));
    }

    /**
     * Enregistrer un nouvel examen
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_patient' => 'required|exists:patients,id_patient',
            'type_examen' => 'required|string|max:100',
            'date_examen' => 'required|date',
            'heure_examen' => 'required',
            'id_maladie' => 'nullable|exists:maladies,id_maladie',
            'commentaires' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $examen = Examen::create([
                'numero_examen' => $this->genererNumeroExamen(),
                'type_examen' => $request->type_examen,
                'date_examen' => $request->date_examen,
                'heure_examen' => $request->heure_examen,
                'id_patient' => $request->id_patient,
                'id_maladie' => $request->id_maladie,
                'laborantin_id' => Auth::id(),
                'statut' => 'en_attente',
                'commentaires' => $request->commentaires,
                // 'priorite' => $request->priorite ?? 'normal', // COMMENTÉ - Colonne n'existe pas
            ]);

            DB::commit();

            return redirect()->route('laborantin.examens.show', $examen->id_examen)
                ->with('success', 'Examen créé avec succès ! Numéro: ' . $examen->numero_examen);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'un examen
     */
    public function show($id)
    {
        $examen = Examen::with(['patient', 'maladie', 'laborantin'])->findOrFail($id);
        
        if ($examen->laborantin_id !== Auth::id() && !Auth::user()->estGestionnaire()) {
            abort(403);
        }
        
        return view('laborantin.examens.show', compact('examen'));
    }

    /**
     * Formulaire de saisie des résultats
     */
    public function saisirResultat($id)
    {
        $examen = Examen::findOrFail($id);
        
        if ($examen->laborantin_id !== Auth::id()) {
            abort(403);
        }
        
        return view('laborantin.examens.resultat', compact('examen'));
    }

    /**
     * Enregistrer les résultats d'examen
     */
    public function updateResultat(Request $request, $id)
    {
        $examen = Examen::findOrFail($id);
        
        if ($examen->laborantin_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'resultat' => 'required|string',
            'statut' => 'required|in:en_attente,termine,annule',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $examen->update([
                'resultat' => $request->resultat,
                'statut' => $request->statut,
            ]);

            // Notifier les médecins
            $medecins = \App\Models\User::where('role_id', 2)->get();
            foreach ($medecins as $medecin) {
                Notification::create([
                    'titre' => '📊 Résultat d\'examen disponible',
                    'message' => 'Le résultat de l\'examen ' . $examen->numero_examen . ' pour le patient ' . ($examen->patient->nom_complet ?? 'N/A') . ' est disponible.',
                    'type' => 'info',
                    'user_id' => $medecin->id_user,
                    'lien' => route('medecin.examens.show', $examen->id_examen),
                    'est_lue' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('laborantin.examens.show', $examen->id_examen)
                ->with('success', 'Résultat enregistré avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Analyser un examen
     */
    public function analyse($id)
    {
        $examen = Examen::with(['patient', 'maladie', 'laborantin'])->findOrFail($id);
        
        if ($examen->laborantin_id !== Auth::id()) {
            abort(403);
        }
        
        return view('laborantin.examens.analyse', compact('examen'));
    }

    /**
     * Imprimer un examen
     */
    public function imprimer($id)
    {
        $examen = Examen::with(['patient', 'maladie', 'laborantin'])->findOrFail($id);
        
        return view('laborantin.examens.print', compact('examen'));
    }

    /**
     * Supprimer un examen
     */
    public function destroy($id)
    {
        $examen = Examen::findOrFail($id);
        
        if ($examen->laborantin_id !== Auth::id()) {
            abort(403);
        }
        
        $examen->delete();

        return redirect()->route('laborantin.examens.index')
            ->with('success', 'Examen supprimé avec succès');
    }

    /**
     * Générer un numéro d'examen unique
     */
    private function genererNumeroExamen()
    {
        $prefix = 'EXAM';
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
        
        return $prefix . '-' . $year . $month . '-' . $newNumber;
    }
}