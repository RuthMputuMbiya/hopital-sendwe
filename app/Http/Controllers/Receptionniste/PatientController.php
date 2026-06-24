<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\BonAnalyse;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
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
        
        $patients = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total' => Patient::count(),
            'aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'hommes' => Patient::where('sexe', 'M')->count(),
            'femmes' => Patient::where('sexe', 'F')->count(),
        ];
        
        return view('receptionniste.patients.index', compact('patients', 'stats', 'search'));
    }

    public function create()
    {
        $medecins = User::where('role_id', 2)->where('est_actif', true)->orderBy('nom')->get();
        return view('receptionniste.patients.create', compact('medecins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'nullable|string|max:100',
            'nationalite' => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|string|max:50',
            'telephone' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'adresse' => 'required|string',
            'ville' => 'nullable|string|max:100',
            'quartier' => 'nullable|string|max:100',
            'antecedents_medicaux' => 'nullable|string',
            'antecedents_chirurgicaux' => 'nullable|string',
            'antecedents_familiaux' => 'nullable|string',
            'allergies' => 'nullable|string',
            'traitements_en_cours' => 'nullable|string',
            'vaccinations' => 'nullable|string',
            'handicap' => 'nullable|string',
            'contact_urgence_nom' => 'nullable|string|max:100',
            'contact_urgence_telephone' => 'nullable|string|max:20',
            'contact_urgence_relation' => 'nullable|string|max:50',
            'medecin_attribue' => 'nullable|exists:users,id_user',
        ]);

        DB::beginTransaction();
        try {
            $numeroFiche = $this->genererNumeroFiche();
            
            $patient = Patient::create([
                'numero_fiche' => $numeroFiche,
                'nom' => strtoupper($validated['nom']),
                'postnom' => strtoupper($validated['postnom']),
                'prenom' => ucfirst(strtolower($validated['prenom'])),
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'nationalite' => $validated['nationalite'] ?? null,
                'profession' => $validated['profession'] ?? null,
                'situation_matrimoniale' => $validated['situation_matrimoniale'] ?? null,
                'telephone' => $validated['telephone'],
                'telephone_secondaire' => $validated['telephone_secondaire'] ?? null,
                'email' => strtolower($validated['email'] ?? ''),
                'adresse' => $validated['adresse'],
                'ville' => $validated['ville'] ?? 'Lubumbashi',
                'quartier' => $validated['quartier'] ?? null,
                'antecedents_medicaux' => $validated['antecedents_medicaux'] ?? null,
                'antecedents_chirurgicaux' => $validated['antecedents_chirurgicaux'] ?? null,
                'antecedents_familiaux' => $validated['antecedents_familiaux'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'traitements_en_cours' => $validated['traitements_en_cours'] ?? null,
                'vaccinations' => $validated['vaccinations'] ?? null,
                'handicap' => $validated['handicap'] ?? null,
                'contact_urgence_nom' => $validated['contact_urgence_nom'] ?? null,
                'contact_urgence_telephone' => $validated['contact_urgence_telephone'] ?? null,
                'contact_urgence_relation' => $validated['contact_urgence_relation'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($request->medecin_attribue) {
                $consultation = Consultation::create([
                    'numero_consultation' => $this->genererNumeroConsultation(),
                    'date_consultation' => now(),
                    'heure_consultation' => now()->format('H:i:s'),
                    'motif' => 'Première consultation - Nouveau patient',
                    'symptomes' => 'À évaluer lors de la consultation',
                    'diagnostic' => '',
                    'prescription' => '',
                    'id_patient' => $patient->id_patient,
                    'medecin_id' => $request->medecin_attribue,
                    'statut' => 'en_cours',
                ]);

                Notification::create([
                    'titre' => '📋 Nouveau patient attribué',
                    'message' => "Le patient {$patient->nom_complet} (N°: {$numeroFiche}) vous a été attribué par " . auth()->user()->nom_complet,
                    'type' => 'info',
                    'user_id' => $request->medecin_attribue,
                    'lien' => route('medecin.patients.show', $patient->id_patient),
                    'est_lue' => false,
                ]);
            }

            Notification::create([
                'titre' => '📝 Nouveau patient enregistré',
                'message' => "Le patient {$patient->nom_complet} (N°: {$numeroFiche}) a été enregistré avec succès",
                'type' => 'success',
                'user_id' => auth()->id(),
                'lien' => route('receptionniste.patients.show', $patient->id_patient),
                'est_lue' => false,
            ]);

            DB::commit();

            return redirect()->route('receptionniste.patients.show', $patient->id_patient)
                ->with('success', 'Patient enregistré avec succès ! Numéro fiche: ' . $numeroFiche);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        $medecinTraitant = $patient->consultations()->latest('date_consultation')->first()?->medecin;
        
        $stats = [
            'total_consultations' => $patient->consultations()->count(),
            'total_examens' => $patient->examens()->count(),
            'total_bons_analyse' => $patient->bonsAnalyse()->count(),
            'age' => $patient->age,
        ];
        
        $consultations = $patient->consultations()->orderBy('date_consultation', 'desc')->paginate(10);
        $examens = $patient->examens()->orderBy('date_examen', 'desc')->paginate(10);
        $bonsAnalyse = $patient->bonsAnalyse()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('receptionniste.patients.show', compact('patient', 'stats', 'consultations', 'examens', 'bonsAnalyse', 'medecinTraitant'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('receptionniste.patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'nullable|string|max:100',
            'nationalite' => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|string|max:50',
            'telephone' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'adresse' => 'required|string',
            'ville' => 'nullable|string|max:100',
            'quartier' => 'nullable|string|max:100',
            'antecedents_medicaux' => 'nullable|string',
            'antecedents_chirurgicaux' => 'nullable|string',
            'antecedents_familiaux' => 'nullable|string',
            'allergies' => 'nullable|string',
            'traitements_en_cours' => 'nullable|string',
            'vaccinations' => 'nullable|string',
            'handicap' => 'nullable|string',
            'contact_urgence_nom' => 'nullable|string|max:100',
            'contact_urgence_telephone' => 'nullable|string|max:20',
            'contact_urgence_relation' => 'nullable|string|max:50',
        ]);

        $patient->update($validated);

        return redirect()->route('receptionniste.patients.show', $patient->id_patient)
            ->with('success', 'Patient modifié avec succès');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        
        if ($patient->consultations()->count() > 0 || $patient->examens()->count() > 0 || $patient->bonsAnalyse()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce patient car il a des consultations, examens ou bons d\'analyse associés.');
        }
        
        $patient->delete();

        return redirect()->route('receptionniste.patients.index')
            ->with('success', 'Patient supprimé avec succès');
    }

    private function genererNumeroFiche()
    {
        $prefix = 'SENDWE';
        $year = date('Y');
        $month = date('m');
        
        $lastPatient = Patient::whereYear('created_at', $year)
            ->orderBy('id_patient', 'desc')
            ->first();
        
        if ($lastPatient) {
            $lastNumber = intval(substr($lastPatient->numero_fiche, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $year . $month . '-' . $newNumber;
    }

    private function genererNumeroConsultation()
    {
        $prefix = 'CONS';
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $lastConsultation = Consultation::whereYear('created_at', $year)
            ->orderBy('id_consultation', 'desc')
            ->first();
        
        if ($lastConsultation && $lastConsultation->numero_consultation) {
            $lastNumber = intval(substr($lastConsultation->numero_consultation, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $year . $month . $day . '-' . $newNumber;
    }
}