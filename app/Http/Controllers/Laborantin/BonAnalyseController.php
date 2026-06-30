<?php

namespace App\Http\Controllers\Laborantin;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use App\Models\Notification;
use App\Models\Bilan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class BonAnalyseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        
        $query = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'bilanInfirmier'])
            ->whereIn('statut', ['en_attente', 'preleve', 'analyse', 'termine', 'retourne']);
        
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
            'preleve' => BonAnalyse::where('statut', 'preleve')->count(),
            'analyse' => BonAnalyse::where('statut', 'analyse')->count(),
            'termines' => BonAnalyse::where('statut', 'termine')->count(),
            'retournes' => BonAnalyse::where('statut', 'retourne')->count(),
        ];
        
        return view('laborantin.bons_analyse.index', compact('bons', 'stats', 'search', 'statut'));
    }

    public function show($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'laborantin', 'bilanInfirmier'])
            ->findOrFail($id);
        
        return view('laborantin.bons_analyse.show', compact('bon'));
    }

    public function preleverForm($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'bilanInfirmier'])
            ->findOrFail($id);
        
        return view('laborantin.bons_analyse.prelever', compact('bon'));
    }

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
                'statut' => 'preleve',
                'laborantin_id' => Auth::id()
            ]);
            
            Notification::create([
                'titre' => '🧪 Prélèvement effectué',
                'message' => "Bon N° {$bon->numero_bon} - Patient: {$bon->patient->nom_complet} - Prélèvement effectué",
                'type' => 'info',
                'user_id' => $bon->medecin_id,
                'lien' => route('medecin.bons-analyse.show', $bon->id_bon),
                'est_lue' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('laborantin.bons-analyse.index')
                ->with('success', '✅ Prélèvement enregistré avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function saisirResultatsForm($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'bilanInfirmier'])
            ->findOrFail($id);
        
        $examensListe = [];
        if ($bon->examens) {
            foreach ($bon->examens as $categorie => $items) {
                foreach ($items as $key => $value) {
                    if ($value == 1) {
                        $examensListe[] = [
                            'key' => $key,
                            'label' => $this->getExamenLabel($key),
                            'categorie' => $categorie,
                            'valeur' => null,
                            'unite' => $this->getExamenUnite($key),
                            'valeurs_normales' => $this->getExamenValeursNormales($key)
                        ];
                    }
                }
            }
        }
        
        return view('laborantin.bons_analyse.resultats', compact('bon', 'examensListe'));
    }

    public function saisirResultats(Request $request, $id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        $request->validate([
            'resultats' => 'required|array',
            'resultats.*.valeur' => 'nullable|string',
            'resultats.*.commentaire' => 'nullable|string',
            'observations_laborantin' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        try {
            $examensListe = $this->getExamensListe($bon);
            $resultats = [];
            
            foreach ($request->resultats as $index => $data) {
                $key = $examensListe[$index]['key'] ?? 'examen_' . $index;
                $resultats[$key] = [
                    'valeur' => $data['valeur'] ?? null,
                    'commentaire' => $data['commentaire'] ?? null,
                    'date_saisie' => now()->format('Y-m-d H:i:s')
                ];
            }
            
            $bon->update([
                'resultats' => $resultats,
                'observations_laborantin' => $request->observations_laborantin,
                'date_retour' => now(),
                'statut' => 'termine'
            ]);
            
            // ✅ NOTIFICATION AU MÉDECIN
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
                ->with('success', '✅ Résultats enregistrés et envoyés au médecin avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    private function getExamensListe($bon)
    {
        $examensListe = [];
        if ($bon->examens) {
            foreach ($bon->examens as $categorie => $items) {
                foreach ($items as $key => $value) {
                    if ($value == 1) {
                        $examensListe[] = [
                            'key' => $key,
                            'label' => $this->getExamenLabel($key),
                            'categorie' => $categorie,
                            'unite' => $this->getExamenUnite($key),
                            'valeurs_normales' => $this->getExamenValeursNormales($key)
                        ];
                    }
                }
            }
        }
        return $examensListe;
    }

    public function retourner($id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        if ($bon->statut != 'termine') {
            return back()->with('error', '⚠️ Les résultats doivent être saisis avant le retour.');
        }
        
        DB::beginTransaction();
        try {
            $bon->update([
                'statut' => 'retourne',
                'date_retour' => now()
            ]);
            
            Notification::create([
                'titre' => '📋 Bon d\'analyse retourné',
                'message' => "Le bon d'analyse N° {$bon->numero_bon} pour le patient {$bon->patient->nom_complet} a été retourné par le laborantin.",
                'type' => 'success',
                'user_id' => $bon->medecin_id,
                'lien' => route('medecin.bons-analyse.show', $bon->id_bon),
                'est_lue' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('laborantin.bons-analyse.index')
                ->with('success', '✅ Bon d\'analyse retourné au médecin avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'infirmier', 'laborantin', 'bilanInfirmier'])
            ->findOrFail($id);
        
        $pdf = PDF::loadView('laborantin.bons_analyse.print', compact('bon'));
        return $pdf->download('bon_analyse_' . $bon->numero_bon . '.pdf');
    }

    public function updateStatut(Request $request, $id)
    {
        $bon = BonAnalyse::findOrFail($id);
        
        $request->validate([
            'statut' => 'required|in:en_attente,preleve,analyse,termine,retourne'
        ]);
        
        $bon->update(['statut' => $request->statut]);
        
        return redirect()->route('laborantin.bons-analyse.show', $bon->id_bon)
            ->with('success', 'Statut mis à jour avec succès.');
    }

    public function getInfirmierNom($bon)
    {
        if ($bon->infirmier) {
            return $bon->infirmier->nom_complet;
        }
        
        if ($bon->id_infirmier) {
            $inf = User::find($bon->id_infirmier);
            if ($inf) {
                return $inf->nom_complet;
            }
        }
        
        if ($bon->bilanInfirmier && $bon->bilanInfirmier->infirmier) {
            return $bon->bilanInfirmier->infirmier->nom_complet;
        }
        
        return 'Non assigné';
    }

    private function getExamenLabel($key)
    {
        $labels = [
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
            'trypanosome' => 'Recherche Trypanosome',
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
            'tcmh' => 'TCMH',
            'gram_ponctions' => 'Gram-Ponctions',
            'cytologie' => 'Cytologie',
            'rivalta' => 'Rivalta',
            'ziehl' => 'Ziehl',
            'elements_gram' => 'Éléments Gram',
            'gram' => 'Gram',
            'glycemie' => 'Glycémie',
            'uree' => 'Urée',
            'creatinine' => 'Créatinine',
            'cholesterol_total' => 'Cholestérol Total',
            'triglycerides' => 'Triglycérides',
            'hdl' => 'HDL-Cholestérol',
            'ldl' => 'LDL-Cholestérol',
            'acide_urique' => 'Acide urique',
            'aghbs' => 'AgHBS',
            'achcv' => 'AcHCV',
            'rpr_vdrl' => 'RPR/VDRL',
            'aslo' => 'ASLO',
            'crp' => 'CRP'
        ];
        return $labels[$key] ?? $key;
    }

    private function getExamenUnite($key)
    {
        $unites = [
            'glycemie' => 'g/L',
            'uree' => 'g/L',
            'creatinine' => 'mg/L',
            'cholesterol_total' => 'g/L',
            'triglycerides' => 'g/L',
            'hdl' => 'g/L',
            'ldl' => 'g/L',
            'acide_urique' => 'mg/L',
            'temperature' => '°C',
            'poids' => 'kg',
            'taille' => 'cm',
            'frequence_cardiaque' => 'bpm',
            'frequence_respiratoire' => '/min',
            'spo2' => '%'
        ];
        return $unites[$key] ?? '';
    }

    private function getExamenValeursNormales($key)
    {
        $normales = [
            'glycemie' => '0.70 - 1.10 g/L',
            'uree' => '0.15 - 0.45 g/L',
            'creatinine' => '5 - 15 mg/L',
            'cholesterol_total' => '1.50 - 2.50 g/L',
            'triglycerides' => '0.40 - 1.60 g/L',
            'hdl' => '> 0.40 g/L',
            'ldl' => '< 1.60 g/L',
            'acide_urique' => '20 - 70 mg/L',
            'temperature' => '36.5 - 37.5 °C',
            'frequence_cardiaque' => '60 - 100 bpm',
            'frequence_respiratoire' => '12 - 20 /min',
            'spo2' => '95 - 100 %'
        ];
        return $normales[$key] ?? '';
    }

    public function getStats()
    {
        return [
            'total' => BonAnalyse::count(),
            'en_attente' => BonAnalyse::where('statut', 'en_attente')->count(),
            'preleve' => BonAnalyse::where('statut', 'preleve')->count(),
            'analyse' => BonAnalyse::where('statut', 'analyse')->count(),
            'termines' => BonAnalyse::where('statut', 'termine')->count(),
            'retournes' => BonAnalyse::where('statut', 'retourne')->count(),
            'urgents' => BonAnalyse::where('priorite', 'urgent')
                ->whereIn('statut', ['en_attente', 'preleve', 'analyse'])->count(),
            'aujourdhui' => BonAnalyse::whereDate('created_at', Carbon::today())->count(),
            'cette_semaine' => BonAnalyse::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'ce_mois' => BonAnalyse::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count(),
        ];
    }
}