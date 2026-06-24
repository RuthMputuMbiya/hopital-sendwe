<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Examen;

class CheckExamenAccess
{
    /**
     * Vérifier que l'utilisateur a accès à l'examen
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $examenId = $request->route('examen') ?? $request->route('id');
        
        if (!$examenId) {
            return $next($request);
        }
        
        $examen = Examen::find($examenId);
        
        if (!$examen) {
            abort(404, 'Examen non trouvé');
        }
        
        // Le gestionnaire a accès à tout
        if ($user->estGestionnaire()) {
            return $next($request);
        }
        
        // Le laborantin qui a fait l'examen y a accès
        if ($user->estLaborantin() && $examen->laborantin_id === $user->id_user) {
            return $next($request);
        }
        
        // Le médecin peut voir les résultats des examens de ses patients
        if ($user->estMedecin()) {
            $hasAccess = $examen->patient->consultations()
                ->where('medecin_id', $user->id_user)
                ->exists();
                
            if ($hasAccess) {
                return $next($request);
            }
        }
        
        abort(403, 'Vous n\'avez pas accès à cet examen.');
    }
}