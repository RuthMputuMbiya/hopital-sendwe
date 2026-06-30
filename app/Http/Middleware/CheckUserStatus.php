<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if (!$user->est_actif) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Votre compte est désactivé.');
            }
        }
        
        return $next($request);
    }
}