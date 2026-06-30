<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $user = Auth::user();
        $allowedRoles = array_map('intval', $roles);
        
        if (in_array($user->role_id, $allowedRoles)) {
            return $next($request);
        }
        
        switch ($user->role_id) {
            case 1: return redirect()->route('gestionnaire.dashboard')->with('error', 'Accès non autorisé.');
            case 2: return redirect()->route('medecin.dashboard')->with('error', 'Accès non autorisé.');
            case 3: return redirect()->route('receptionniste.dashboard')->with('error', 'Accès non autorisé.');
            case 4: return redirect()->route('laborantin.dashboard')->with('error', 'Accès non autorisé.');
            case 5: return redirect()->route('infirmier.dashboard')->with('error', 'Accès non autorisé.');
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Rôle non reconnu.');
        }
    }
}