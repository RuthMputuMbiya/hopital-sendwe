<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // POUR TOUS LES UTILISATEURS (médecins, réceptionnistes, laborantins, gestionnaires)
        if (auth()->check()) {
            $user = auth()->user();
            
            // Vérifier si la méthode needsPasswordChange existe
            if (method_exists($user, 'needsPasswordChange') && $user->needsPasswordChange()) {
                // Si déjà sur la page de changement, on laisse passer
                if ($request->route()->getName() == 'password.change') {
                    return $next($request);
                }
                
                // Sinon, rediriger vers la page de changement
                return redirect()->route('password.change')
                    ->with('warning', '⚠️ Veuillez changer votre mot de passe avant de continuer.');
            }
        }
        
        return $next($request);
    }
}