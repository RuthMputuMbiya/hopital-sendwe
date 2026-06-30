<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Redirection selon le rôle
        if ($user->role_id == 1) {
            return redirect()->route('gestionnaire.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('medecin.dashboard');
        } elseif ($user->role_id == 3) {
            return redirect()->route('receptionniste.dashboard');
        } elseif ($user->role_id == 4) {
            return redirect()->route('laborantin.dashboard');
        } elseif ($user->role_id == 5) {
            return redirect()->route('infirmier.dashboard');
        }
        
        // Si le rôle n'est pas reconnu
        Auth::logout();
        return redirect()->route('login')->with('error', 'Rôle non reconnu.');
    }
}