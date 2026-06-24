<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\BonAnalyse;
use Illuminate\Http\Request;

class BonAnalyseController extends Controller
{
    /**
     * Liste des bons d'analyse
     */
    public function index(Request $request)
    {
        $query = BonAnalyse::with(['patient', 'medecin']);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('numero_bon', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('patient', function($q2) use ($request) {
                      $q2->where('nom', 'LIKE', "%{$request->search}%")
                         ->orWhere('prenom', 'LIKE', "%{$request->search}%");
                  });
            });
        }
        
        $bons = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('receptionniste.bons-analyse.index', compact('bons'));
    }

    /**
     * Afficher les détails d'un bon d'analyse
     */
    public function show($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'examens'])->findOrFail($id);
        return view('receptionniste.bons-analyse.show', compact('bon'));
    }

    /**
     * Imprimer un bon d'analyse
     */
    public function print($id)
    {
        $bon = BonAnalyse::with(['patient', 'medecin', 'examens'])->findOrFail($id);
        return view('receptionniste.bons-analyse.print', compact('bon'));
    }
}