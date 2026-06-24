<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs (sans role_id car il est déterminé automatiquement)
        $request->validate([
            'matricule' => ['required', 'string', 'max:50', 'unique:users,matricule'],
            'nom' => ['required', 'string', 'max:100'],
            'postnom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:users,email'],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse' => ['required', 'string'],
            'specialite' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // DÉTERMINER LE RÔLE AUTOMATIQUEMENT À PARTIR DU MATRICULE
        $matricule = strtoupper($request->matricule);
        $roleId = $this->determineRoleFromMatricule($matricule);

        // Vérifier que le rôle existe
        $role = Role::find($roleId);
        if (!$role) {
            throw ValidationException::withMessages([
                'matricule' => ['Le matricule doit commencer par ADM, DOC, LAB ou REC'],
            ]);
        }

        // Créer l'utilisateur
        $user = User::create([
            'matricule' => $matricule,
            'nom' => strtoupper($request->nom),
            'postnom' => strtoupper($request->postnom),
            'prenom' => ucfirst(strtolower($request->prenom)),
            'sexe' => $request->sexe,
            'email' => strtolower($request->email),
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'est_actif' => true,
            'date_embauche' => now(),
            'specialite' => ($roleId == 2) ? $request->specialite : null,
        ]);

        event(new Registered($user));

        // Connecter l'utilisateur directement
        Auth::login($user);

        // Rediriger vers le bon tableau de bord selon le rôle
        return redirect()->intended($this->redirectTo($user));
    }

    /**
     * Déterminer le rôle à partir du matricule
     */
    protected function determineRoleFromMatricule(string $matricule): int
    {
        if (str_starts_with($matricule, 'ADM')) {
            return 1; // Gestionnaire / Admin
        } elseif (str_starts_with($matricule, 'DOC')) {
            return 2; // Médecin
        } elseif (str_starts_with($matricule, 'REC')) {
            return 3; // Réceptionniste
        } elseif (str_starts_with($matricule, 'LAB')) {
            return 4; // Laborantin
        }
        
        // Par défaut, retourner un rôle invalide qui déclenchera une erreur
        return 0;
    }

    /**
     * Redirection selon le rôle
     */
    protected function redirectTo($user): string
    {
        if ($user->role_id == 1) {
            return route('gestionnaire.dashboard');
        } elseif ($user->role_id == 2) {
            return route('medecin.dashboard');
        } elseif ($user->role_id == 3) {
            return route('receptionniste.dashboard');
        } elseif ($user->role_id == 4) {
            return route('laborantin.dashboard');
        }
        
        return route('dashboard');
    }
}