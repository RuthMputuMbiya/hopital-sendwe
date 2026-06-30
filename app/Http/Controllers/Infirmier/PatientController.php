<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function attente()
    {
        $patients = Consultation::where('statut_consultation', 'en_attente_infirmier')
            ->with(['patient', 'medecin'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);
            
        return view('infirmier.patients.attente', compact('patients'));
    }

    public function bilanForm($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $consultation = Consultation::where('id_patient', $patientId)
            ->where('statut_consultation', 'en_attente_infirmier')
            ->first();
            
        if (!$consultation) {
            return redirect()->route('infirmier.dashboard')
                ->with('error', 'Aucune consultation en attente pour ce patient.');
        }
        
        return view('infirmier.patients.bilan', compact('patient', 'consultation'));
    }

    public function storeBilan(Request $request, $patientId)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id_consultation',
            'poids' => 'nullable|numeric|min:1|max:300',
            'taille' => 'nullable|numeric|min:1|max:300',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'tension_arterielle' => 'nullable|string|max:20',
            'frequence_cardiaque' => 'nullable|integer|min:20|max:300',
            'frequence_respiratoire' => 'nullable|integer|min:4|max:60',
            'motif' => 'nullable|string',
            'symptomes' => 'nullable|string',
            'observations' => 'nullable|string',
            'allergies' => 'nullable|string',
            'traitements_en_cours' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $consultation = Consultation::findOrFail($validated['consultation_id']);
            
            // ✅ METTRE À JOUR LA CONSULTATION AVEC id_infirmier
            $consultation->update([
                'id_infirmier' => Auth::id(),  // ✅ id_infirmier
                'statut_consultation' => 'bilan_fait',
                'poids' => $validated['poids'] ?? null,
                'taille' => $validated['taille'] ?? null,
                'temperature' => $validated['temperature'] ?? null,
                'tension_arterielle' => $validated['tension_arterielle'] ?? null,
                'frequence_cardiaque' => $validated['frequence_cardiaque'] ?? null,
            ]);

            // Notifier le médecin
            if ($consultation->medecin_id) {
                Notification::create([
                    'titre' => '📋 Nouveau bilan disponible',
                    'message' => "Le bilan du patient {$consultation->patient->nom_complet} est prêt.",
                    'type' => 'info',
                    'user_id' => $consultation->medecin_id,
                    'lien' => route('medecin.consultations.show', $consultation->id_consultation),
                    'est_lue' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('infirmier.dashboard')
                ->with('success', '✅ Bilan enregistré avec succès ! Le patient a été transmis au médecin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement du bilan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $consultations = $patient->consultations()
            ->with(['medecin', 'infirmier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('infirmier.patients.show', compact('patient', 'consultations'));
    }
}