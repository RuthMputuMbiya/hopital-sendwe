<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\FichierPatient;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FichierPatientController extends Controller
{
    public function index($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $fichiers = FichierPatient::where('id_patient', $patientId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total' => $fichiers->total(),
            'images' => FichierPatient::where('id_patient', $patientId)->where('type_fichier', 'image')->count(),
            'pdfs' => FichierPatient::where('id_patient', $patientId)->where('type_fichier', 'pdf')->count(),
            'documents' => FichierPatient::where('id_patient', $patientId)->where('type_fichier', 'document')->count(),
        ];
        
        return view('receptionniste.fichiers.index', compact('patient', 'fichiers', 'stats'));
    }

    public function create($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        return view('receptionniste.fichiers.create', compact('patient'));
    }

    public function store(Request $request, $patientId)
    {
        $request->validate([
            'fichier' => 'required|file|max:10240',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_fichier' => 'required|in:image,pdf,document,radiologie',
        ]);

        $patient = Patient::findOrFail($patientId);
        $file = $request->file('fichier');
        $extension = $file->getClientOriginalExtension();
        
        // Créer le dossier s'il n'existe pas
        $path = 'fichiers_patients/' . $patient->id_patient;
        $nomFichier = time() . '_' . uniqid() . '.' . $extension;
        $chemin = $file->storeAs($path, $nomFichier, 'public');
        
        DB::beginTransaction();
        try {
            $fichier = FichierPatient::create([
                'id_patient' => $patient->id_patient,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $chemin,
                'type_fichier' => $request->type_fichier,
                'extension' => $extension,
                'taille' => $file->getSize(),
                'titre' => $request->titre,
                'description' => $request->description,
                'uploaded_by' => Auth::id(),
            ]);
            
            // Notifier les médecins
            $medecins = \App\Models\User::where('role_id', 2)->get();
            foreach ($medecins as $medecin) {
                Notification::create([
                    'titre' => '📎 Nouveau document médical',
                    'message' => "Un nouveau document '{$fichier->titre}' a été ajouté au dossier de {$patient->nom_complet}",
                    'type' => 'info',
                    'user_id' => $medecin->id_user,
                    'lien' => route('medecin.patients.show', $patient->id_patient),
                    'est_lue' => false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('receptionniste.fichiers.index', $patient->id_patient)
                ->with('success', 'Document ajouté avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('public')->delete($chemin);
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $fichier = FichierPatient::findOrFail($id);
        return view('receptionniste.fichiers.show', compact('fichier'));
    }

    public function download($id)
    {
        $fichier = FichierPatient::findOrFail($id);
        return Storage::disk('public')->download($fichier->chemin_fichier, $fichier->nom_fichier);
    }

    public function destroy($id)
    {
        $fichier = FichierPatient::findOrFail($id);
        Storage::disk('public')->delete($fichier->chemin_fichier);
        $fichier->delete();
        
        return redirect()->route('receptionniste.fichiers.index', $fichier->id_patient)
            ->with('success', 'Document supprimé avec succès');
    }
}