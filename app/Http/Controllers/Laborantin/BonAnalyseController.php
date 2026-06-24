<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BonAnalyseController extends Controller
{
    /**
     * Liste des bons d'analyse reçus
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        
        $query = BonAnalyse::with(['patient', 'medecin']);
        
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
            'total' => BonAnalyse::count(),
            'en_attente' => BonAnalyse::where('statut', 'en_attente')->count(),
            'en_cours' => BonAnalyse::where('statut', 'en_cours')->count(),
            'termines' => BonAnalyse::where('statut', 'termine')->count(),
        ];
        
        return view('laborantin.bons_analyse.index', compact('bons', 'stats', 'search', 'statut'));
    }

    /**
     * Afficher les détails d'un bon
     */
    public function show($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin'])->findOrFail($id);
        return view('laborantin.bons_analyse.show', compact('bon'));
    }

    /**
     * Formulaire de prélèvement
     */
    public function preleverForm($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin'])->findOrFail($id);
        return view('laborantin.bons_analyse.prelever', compact('bon'));
    }

    /**
     * Enregistrer le prélèvement
     */
    public function prelever(Request $request, $id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        $request->validate([
            'nom_preleveur' => 'required|string|max:100',
            'prenom_preleveur' => 'required|string|max:100',
            'date_prelevement' => 'required|date',
            'heure_prelevement' => 'required',
            'id_client_labo' => 'nullable|string|max:50',
            'telephone_preleveur' => 'nullable|string|max:20'
        ]);
        
        DB::beginTransaction();
        try {
            $bon->update([
                'nom_preleveur' => $request->nom_preleveur,
                'prenom_preleveur' => $request->prenom_preleveur,
                'date_prelevement' => $request->date_prelevement,
                'heure_prelevement' => $request->heure_prelevement,
                'id_client_labo' => $request->id_client_labo,
                'telephone_preleveur' => $request->telephone_preleveur,
                'statut' => 'en_cours',
                'laborantin_id' => Auth::id()
            ]);
            
            DB::commit();
            
            return redirect()->route('laborantin.bons-analyse.index')
                ->with('success', 'Prélèvement enregistré avec succès ! Bon en cours de traitement.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Saisir les résultats
     */
    public function saisirResultats(Request $request, $id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        $request->validate([
            'resultats' => 'required|array',
            'observations_laborantin' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        try {
            $bon->update([
                'resultats' => $request->resultats,
                'observations_laborantin' => $request->observations_laborantin,
                'date_retour' => now(),
                'statut' => 'termine'
            ]);
            
            Notification::create([
                'titre' => '📊 Résultats d\'analyse disponibles',
                'message' => "Bon N° {$bon->numero_bon} - Patient: {$bon->patient->nom_complet} - Résultats disponibles",
                'type' => 'success',
                'user_id' => $bon->medecin_id,
                'lien' => route('medecin.bons-analyse.show', $bon->id_bon),
                'est_lue' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('laborantin.bons-analyse.index')
                ->with('success', 'Résultats enregistrés et envoyés au médecin.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Retourner le bon au médecin
     */
    public function retourner($id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        if ($bon->statut != 'termine') {
            return back()->with('error', 'Les résultats doivent être saisis avant le retour.');
        }
        
        return redirect()->route('laborantin.bons-analyse.index')
            ->with('success', 'Bon retourné au médecin.');
    }

    /**
     * Imprimer le bon
     */
    public function print($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'laborantin'])->findOrFail($id);
        return view('laborantin.bons_analyse.print', compact('bon'));
    }
}