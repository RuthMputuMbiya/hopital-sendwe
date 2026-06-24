<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Afficher le formulaire de changement de mot de passe.
     */
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    /**
     * Traiter le changement de mot de passe.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('Le mot de passe actuel est incorrect.');
                }
            }],
            'new_password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);

        $user = auth()->user();
        
        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->markPasswordAsChanged(); // Marquer comme changé
        $user->save();

        // Rediriger vers le dashboard selon le rôle
        return redirect()->route($this->getDashboardRoute($user))
            ->with('success', '✅ Votre mot de passe a été changé avec succès.');
    }

    /**
     * Obtenir la route du dashboard selon le rôle de l'utilisateur.
     */
    private function getDashboardRoute($user)
    {
        $role = $user->getRoleFromMatricule();
        
        $dashboards = [
            'gestionnaire' => 'gestionnaire.dashboard',
            'medecin' => 'medecin.dashboard',
            'laborantin' => 'laborantin.dashboard',
            'receptionniste' => 'receptionniste.dashboard'
        ];
        
        return $dashboards[$role] ?? 'dashboard';
    }
}