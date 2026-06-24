<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamenController extends Controller
{
    /**
     * Liste des examens (résultats disponibles)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut', 'termine');
        
        $query = Examen::with(['patient', 'laborantin', 'maladie']);
        
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
        
        if ($statut && $statut != 'all') {
            $query->where('statut', $statut);
        }
        
        $examens = $query->orderBy('date_examen', 'desc')->paginate(15);
        
        $stats = [
            'total' => Examen::where('statut', 'termine')->count(),
            'aujourdhui' => Examen::whereDate('date_examen', today())->where('statut', 'termine')->count(),
            'semaine' => Examen::whereBetween('date_examen', [now()->startOfWeek(), now()->endOfWeek()])->where('statut', 'termine')->count(),
        ];
        
        return view('receptionniste.examens.index', compact('examens', 'stats', 'search', 'statut'));
    }

    /**
     * Afficher le détail d'un examen (résultat)
     */
    public function show($id)
    {
        $examen = Examen::with(['patient', 'laborantin', 'maladie'])->findOrFail($id);
        
        return view('receptionniste.examens.show', compact('examen'));
    }

    /**
     * Imprimer le résultat d'examen
     */
    public function print($id)
    {
        $examen = Examen::with(['patient', 'laborantin', 'maladie'])->findOrFail($id);
        
        return view('receptionniste.examens.print', compact('examen'));
    }
}