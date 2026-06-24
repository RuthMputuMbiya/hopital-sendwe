<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;

class CheckConsultationAccess
{
    /**
     * Vérifier que l'utilisateur a accès à la consultation
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $consultationId = $request->route('consultation') ?? $request->route('id');
        
        if (!$consultationId) {
            return $next($request);
        }
        
        $consultation = Consultation::find($consultationId);
        
        if (!$consultation) {
            abort(404, 'Consultation non trouvée');
        }
        
        // Le gestionnaire a accès à tout
        if ($user->estGestionnaire()) {
            return $next($request);
        }
        
        // Le médecin qui a fait la consultation y a accès
        if ($user->estMedecin() && $consultation->medecin_id === $user->id_user) {
            return $next($request);
        }
        
        // Le réceptionniste peut voir toutes les consultations
        if ($user->estReceptionniste()) {
            return $next($request);
        }
        
        abort(403, 'Vous n\'avez pas accès à cette consultation.');
    }
}