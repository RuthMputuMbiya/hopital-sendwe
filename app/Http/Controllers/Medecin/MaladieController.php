<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Maladie;
use App\Models\Consultation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaladieController extends Controller
{
    // Supprimez le constructeur avec middleware()
    // La vérification se fait déjà dans les routes

    /**
     * Liste des maladies
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categorie = $request->get('categorie');
        
        $query = Maladie::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom_maladie', 'LIKE', "%{$search}%")
                  ->orWhere('code_maladie', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('symptomes', 'LIKE', "%{$search}%");
            });
        }
        
        if ($categorie) {
            $query->where('categorie', $categorie);
        }
        
        $maladies = $query->orderBy('nom_maladie', 'asc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Maladie::count(),
            'categories' => Maladie::select('categorie', DB::raw('count(*) as total'))
                ->groupBy('categorie')
                ->get(),
            'plus_frequentes' => Consultation::whereNotNull('id_maladie')
                ->select('id_maladie', DB::raw('count(*) as total'))
                ->groupBy('id_maladie')
                ->with('maladie')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];
        
        // Liste des catégories pour le filtre
        $categories = Maladie::distinct()->pluck('categorie')->filter();
        
        return view('medecin.maladies.index', compact('maladies', 'stats', 'search', 'categorie', 'categories'));
    }

    /**
     * Formulaire de création d'une maladie
     */
    public function create()
    {
        return view('medecin.maladies.create');
    }

    /**
     * Enregistrer une nouvelle maladie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_maladie' => 'required|string|max:50|unique:maladies,code_maladie',
            'nom_maladie' => 'required|string|max:150',
            'description' => 'required|string',
            'categorie' => 'nullable|string|max:100',
            'symptomes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $maladie = Maladie::create([
                'code_maladie' => strtoupper($validated['code_maladie']),
                'nom_maladie' => ucfirst($validated['nom_maladie']),
                'description' => $validated['description'],
                'categorie' => $validated['categorie'],
                'symptomes' => $validated['symptomes'],
            ]);

            // Notification
            Notification::create([
                'titre' => '🆕 Nouvelle maladie ajoutée',
                'message' => "La maladie '{$maladie->nom_maladie}' a été ajoutée par Dr. " . auth()->user()->nom_complet,
                'type' => 'info',
                'user_id' => auth()->id(),
                'lien' => route('medecin.maladies.show', $maladie->id_maladie),
            ]);

            DB::commit();

            return redirect()->route('medecin.maladies.index')
                ->with('success', 'Maladie ajoutée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'une maladie
     */
    public function show($id)
    {
        $maladie = Maladie::withCount('consultations')->findOrFail($id);
        
        // Statistiques des consultations pour cette maladie
        $consultationsParMois = Consultation::where('id_maladie', $id)
            ->select(DB::raw('YEAR(date_consultation) as annee'), DB::raw('MONTH(date_consultation) as mois'), DB::raw('count(*) as total'))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->limit(6)
            ->get();
        
        $dernieresConsultations = Consultation::with(['patient', 'medecin'])
            ->where('id_maladie', $id)
            ->orderBy('date_consultation', 'desc')
            ->limit(10)
            ->get();
        
        return view('medecin.maladies.show', compact('maladie', 'consultationsParMois', 'dernieresConsultations'));
    }

    /**
     * Formulaire de modification d'une maladie
     */
    public function edit($id)
    {
        $maladie = Maladie::findOrFail($id);
        return view('medecin.maladies.edit', compact('maladie'));
    }

    /**
     * Mettre à jour une maladie
     */
    public function update(Request $request, $id)
    {
        $maladie = Maladie::findOrFail($id);

        $validated = $request->validate([
            'code_maladie' => ['required', 'string', 'max:50', Rule::unique('maladies', 'code_maladie')->ignore($maladie->id_maladie, 'id_maladie')],
            'nom_maladie' => 'required|string|max:150',
            'description' => 'required|string',
            'categorie' => 'nullable|string|max:100',
            'symptomes' => 'nullable|string',
        ]);

        $maladie->update([
            'code_maladie' => strtoupper($validated['code_maladie']),
            'nom_maladie' => ucfirst($validated['nom_maladie']),
            'description' => $validated['description'],
            'categorie' => $validated['categorie'],
            'symptomes' => $validated['symptomes'],
        ]);

        return redirect()->route('medecin.maladies.index')
            ->with('success', 'Maladie modifiée avec succès');
    }

    /**
     * Supprimer une maladie
     */
    public function destroy($id)
    {
        $maladie = Maladie::findOrFail($id);
        
        // Vérifier si la maladie est utilisée dans des consultations
        if ($maladie->consultations()->count() > 0) {
            return back()->with('error', 'Cette maladie ne peut pas être supprimée car elle est utilisée dans ' . $maladie->consultations()->count() . ' consultation(s).');
        }
        
        $maladie->delete();

        return redirect()->route('medecin.maladies.index')
            ->with('success', 'Maladie supprimée avec succès');
    }

    /**
     * Recherche AJAX pour les maladies
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $maladies = Maladie::where('nom_maladie', 'LIKE', "%{$search}%")
            ->orWhere('code_maladie', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['id_maladie', 'nom_maladie', 'code_maladie']);
        
        return response()->json($maladies);
    }
}