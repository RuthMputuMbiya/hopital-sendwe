<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur est bien connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Vérifier que l'utilisateur est bien un réceptionniste
        if (Auth::user()->role_id != 3) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('gestionnaire.dashboard');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('medecin.dashboard');
            } elseif (Auth::user()->role_id == 4) {
                return redirect()->route('laborantin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $stats = [
            'total_patients' => Patient::count(),
            'patients_aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'patients_semaine' => Patient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'patients_mois' => Patient::whereMonth('created_at', now()->month)->count(),
        ];

        $recentPatients = Patient::orderBy('created_at', 'desc')->limit(10)->get();

        return view('receptionniste.dashboard', compact('stats', 'recentPatients'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $patients = Patient::where('nom', 'LIKE', "%{$search}%")
            ->orWhere('postnom', 'LIKE', "%{$search}%")
            ->orWhere('prenom', 'LIKE', "%{$search}%")
            ->orWhere('numero_fiche', 'LIKE', "%{$search}%")
            ->orWhere('telephone', 'LIKE', "%{$search}%")
            ->paginate(15);

        return view('receptionniste.patients.index', compact('patients', 'search'));
    }
}