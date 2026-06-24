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

class AgendaController extends Controller
{
    /**
     * Afficher l'agenda des rendez-vous
     */
    public function index(Request $request)
    {
        // Récupérer la date sélectionnée ou la date du jour
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $vue = $request->get('vue', 'jour'); // jour, semaine, mois
        
        // Rendez-vous du jour
        $consultationsJour = Consultation::with(['patient', 'medecin'])
            ->whereDate('date_consultation', $date)
            ->orderBy('heure_consultation', 'asc')
            ->get();
        
        // Examens du jour
        $examensJour = Examen::with(['patient', 'laborantin'])
            ->whereDate('date_examen', $date)
            ->orderBy('heure_examen', 'asc')
            ->get();
        
        // Statistiques du jour
        $stats = [
            'total_consultations' => $consultationsJour->count(),
            'total_examens' => $examensJour->count(),
            'patients_attendus' => $consultationsJour->where('statut', 'en_cours')->count(),
            'examens_attente' => $examensJour->where('statut', 'en_attente')->count(),
        ];
        
        // Données pour la vue semaine
        $semaine = [];
        if ($vue == 'semaine') {
            $debutSemaine = $date->copy()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $jour = $debutSemaine->copy()->addDays($i);
                $semaine[] = [
                    'date' => $jour,
                    'consultations' => Consultation::whereDate('date_consultation', $jour)->count(),
                    'examens' => Examen::whereDate('date_examen', $jour)->count(),
                ];
            }
        }
        
        // Données pour la vue mois
        $mois = [];
        if ($vue == 'mois') {
            $debutMois = $date->copy()->startOfMonth();
            $finMois = $date->copy()->endOfMonth();
            for ($i = 0; $i < $finMois->day; $i++) {
                $jour = $debutMois->copy()->addDays($i);
                $mois[] = [
                    'date' => $jour,
                    'consultations' => Consultation::whereDate('date_consultation', $jour)->count(),
                    'examens' => Examen::whereDate('date_examen', $jour)->count(),
                ];
            }
        }
        
        // Médecins disponibles pour les rendez-vous
        $medecins = User::where('role_id', 2)->where('est_actif', true)->get();
        
        return view('receptionniste.agenda.index', compact(
            'date', 'vue', 'consultationsJour', 'examensJour', 
            'stats', 'semaine', 'mois', 'medecins'
        ));
    }

    /**
     * Formulaire de création d'un rendez-vous
     */
    public function create()
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = User::where('role_id', 2)->where('est_actif', true)->get();
        
        return view('receptionniste.agenda.create', compact('patients', 'medecins'));
    }

    /**
     * Enregistrer un nouveau rendez-vous
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|exists:patients,id_patient',
            'id_medecin' => 'required|exists:users,id_user',
            'date_consultation' => 'required|date',
            'heure_consultation' => 'required',
            'motif' => 'required|string|max:255',
        ]);

        // Vérifier si le médecin est déjà occupé à cette heure
        $existingConsultation = Consultation::where('medecin_id', $request->id_medecin)
            ->whereDate('date_consultation', $request->date_consultation)
            ->where('heure_consultation', $request->heure_consultation)
            ->first();

        if ($existingConsultation) {
            return back()->with('error', 'Ce médecin a déjà un rendez-vous à cette heure.')
                ->withInput();
        }

        // Créer la consultation
        $consultation = Consultation::create([
            'numero_consultacion' => $this->genererNumeroConsultation(),
            'date_consultation' => $request->date_consultation,
            'heure_consultation' => $request->heure_consultation,
            'motif' => $request->motif,
            'symptomes' => $request->symptomes ?? '',
            'diagnostic' => '',
            'prescription' => '',
            'id_patient' => $request->id_patient,
            'medecin_id' => $request->id_medecin,
            'statut' => 'en_cours',
        ]);

        return redirect()->route('receptionniste.agenda.index')
            ->with('success', 'Rendez-vous créé avec succès ! N°: ' . $consultation->numero_consultacion);
    }

    /**
     * Afficher les détails d'un rendez-vous
     */
    public function show($id)
    {
        $consultation = Consultation::with(['patient', 'medecin'])
            ->findOrFail($id);
        
        return view('receptionniste.agenda.show', compact('consultation'));
    }

    /**
     * Modifier un rendez-vous
     */
    public function edit($id)
    {
        $consultation = Consultation::findOrFail($id);
        $patients = Patient::orderBy('nom')->get();
        $medecins = User::where('role_id', 2)->where('est_actif', true)->get();
        
        return view('receptionniste.agenda.edit', compact('consultation', 'patients', 'medecins'));
    }

    /**
     * Mettre à jour un rendez-vous
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $request->validate([
            'id_patient' => 'required|exists:patients,id_patient',
            'id_medecin' => 'required|exists:users,id_user',
            'date_consultation' => 'required|date',
            'heure_consultation' => 'required',
            'motif' => 'required|string|max:255',
        ]);

        // Vérifier si le médecin est déjà occupé à cette heure (sauf pour ce rendez-vous)
        $existingConsultation = Consultation::where('medecin_id', $request->id_medecin)
            ->whereDate('date_consultation', $request->date_consultation)
            ->where('heure_consultation', $request->heure_consultation)
            ->where('id_consultation', '!=', $id)
            ->first();

        if ($existingConsultation) {
            return back()->with('error', 'Ce médecin a déjà un rendez-vous à cette heure.')
                ->withInput();
        }

        $consultation->update([
            'id_patient' => $request->id_patient,
            'medecin_id' => $request->id_medecin,
            'date_consultation' => $request->date_consultation,
            'heure_consultation' => $request->heure_consultation,
            'motif' => $request->motif,
        ]);

        return redirect()->route('receptionniste.agenda.index')
            ->with('success', 'Rendez-vous modifié avec succès !');
    }

    /**
     * Annuler un rendez-vous
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return redirect()->route('receptionniste.agenda.index')
            ->with('success', 'Rendez-vous annulé avec succès !');
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
}