<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Models\Bilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BilanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Bilan::with(['consultation.patient', 'infirmier']);
            
            if ($request->search) {
                $query->whereHas('consultation.patient', function($q) use ($request) {
                    $q->where('nom', 'LIKE', "%{$request->search}%")
                      ->orWhere('prenom', 'LIKE', "%{$request->search}%");
                });
            }
            
            $bilans = $query->orderBy('created_at', 'desc')->paginate(15);
            
            $stats = [
                'total' => Bilan::count(),
                'transmis' => Bilan::where('est_transmis', true)->count(),
                'non_transmis' => Bilan::where('est_transmis', false)->count(),
            ];
            
            return view('infirmier.bilans.index', compact('bilans', 'stats'));
            
        } catch (\Exception $e) {
            return redirect()->route('infirmier.dashboard')
                ->with('warning', 'La fonctionnalité des bilans n\'est pas encore disponible.');
        }
    }

    public function show($id)
    {
        try {
            $bilan = Bilan::with(['consultation.patient', 'consultation.medecin', 'infirmier'])->findOrFail($id);
            return view('infirmier.bilans.show', compact('bilan'));
        } catch (\Exception $e) {
            return redirect()->route('infirmier.bilans.index')
                ->with('error', 'Bilan non trouvé.');
        }
    }

    public function print($id)
    {
        try {
            $bilan = Bilan::with(['consultation.patient', 'consultation.medecin', 'infirmier'])->findOrFail($id);
            return view('infirmier.bilans.print', compact('bilan'));
        } catch (\Exception $e) {
            return redirect()->route('infirmier.bilans.index')
                ->with('error', 'Bilan non trouvé.');
        }
    }
}