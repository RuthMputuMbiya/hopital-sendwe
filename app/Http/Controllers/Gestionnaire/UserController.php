<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleFilter = $request->get('role');
        
        $query = User::with('role');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('postnom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        if ($roleFilter) {
            $query->where('role_id', $roleFilter);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $roles = Role::all();
        $stats = [
            'total' => User::count(),
            'actifs' => User::where('est_actif', true)->count(),
            'inactifs' => User::where('est_actif', false)->count(),
            'medecins' => User::where('role_id', 2)->count(),
            'receptionnistes' => User::where('role_id', 3)->count(),
            'laborantins' => User::where('role_id', 4)->count(),
            'infirmiers' => User::where('role_id', 5)->count(), // ✅ AJOUT INFIRMIER
        ];
        
        return view('gestionnaire.users.index', compact('users', 'roles', 'stats', 'search', 'roleFilter'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $roles = Role::where('id_role', '!=', 1)->get();
        return view('gestionnaire.users.create', compact('roles'));
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|max:50|unique:users,matricule',
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
            'role_id' => 'required|exists:roles,id_role',
            'specialite' => 'nullable|string|max:100',
            'est_actif' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'matricule' => strtoupper($validated['matricule']),
                'nom' => strtoupper($validated['nom']),
                'postnom' => strtoupper($validated['postnom']),
                'prenom' => ucfirst(strtolower($validated['prenom'])),
                'sexe' => $validated['sexe'],
                'email' => strtolower($validated['email']),
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'password' => Hash::make('Password@123'),
                'role_id' => $validated['role_id'],
                'est_actif' => $validated['est_actif'] ?? true,
                'date_embauche' => now(),
                'specialite' => ($validated['role_id'] == 2) ? $validated['specialite'] : null,
                'first_login' => true,
                'password_changed_at' => null
            ]);

            Notification::create([
                'titre' => 'Bienvenue à l\'Hôpital Sendwe',
                'message' => 'Votre compte a été créé par l\'administrateur. Mot de passe temporaire: Password@123',
                'type' => 'success',
                'user_id' => $user->id_user,
                'lien' => route('profile.edit'),
            ]);

            DB::commit();

            return redirect()->route('gestionnaire.users.index')
                ->with('success', 'Utilisateur créé avec succès ! Mot de passe temporaire: Password@123');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(int $id)
    {
        $user = User::with('role')->findOrFail($id);
        
        $stats = [];
        if ($user->role_id == 2) { // Médecin
            $stats['consultations'] = $user->consultationsEffectuees()->count();
            $stats['patients_vus'] = $user->consultationsEffectuees()->distinct('patient_id')->count('patient_id');
        } elseif ($user->role_id == 4) { // Laborantin
            $stats['examens'] = $user->examensEffectues()->count();
            $stats['examens_termines'] = $user->examensEffectues()->where('statut', 'termine')->count();
        } elseif ($user->role_id == 3) { // Réceptionniste
            $stats['patients_enregistres'] = \App\Models\Patient::count();
        } elseif ($user->role_id == 5) { // ✅ INFIRMIER
            $stats['bilans_realises'] = \App\Models\Bilan::where('infirmier_id', $user->id_user)->count();
            $stats['patients_pris_en_charge'] = \App\Models\Consultation::where('infirmier_id', $user->id_user)->distinct('id_patient')->count('id_patient');
        }
        
        $notifications = Notification::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('gestionnaire.users.show', compact('user', 'stats', 'notifications'));
    }

    /**
     * Formulaire de modification
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        
        return view('gestionnaire.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
            'role_id' => 'required|exists:roles,id_role',
            'specialite' => 'nullable|string|max:100',
            'est_actif' => 'boolean',
        ]);

        $user->update([
            'nom' => strtoupper($validated['nom']),
            'postnom' => strtoupper($validated['postnom']),
            'prenom' => ucfirst(strtolower($validated['prenom'])),
            'sexe' => $validated['sexe'],
            'email' => strtolower($validated['email']),
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'role_id' => $validated['role_id'],
            'est_actif' => $validated['est_actif'],
            'specialite' => ($validated['role_id'] == 2) ? $validated['specialite'] : null,
        ]);

        return redirect()->route('gestionnaire.users.index')
            ->with('success', 'Utilisateur modifié avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('gestionnaire.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);
        $newPassword = 'Password@123';
        
        $user->update([
            'password' => Hash::make($newPassword),
            'first_login' => true,
            'password_changed_at' => null
        ]);

        Notification::create([
            'titre' => 'Mot de passe réinitialisé',
            'message' => 'Votre mot de passe a été réinitialisé. Nouveau mot de passe: ' . $newPassword,
            'type' => 'warning',
            'user_id' => $user->id_user,
            'lien' => route('profile.edit'),
        ]);

        return back()->with('success', 'Mot de passe réinitialisé avec succès');
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre statut');
        }
        
        $user->update(['est_actif' => !$user->est_actif]);
        
        $status = $user->est_actif ? 'activé' : 'désactivé';
        
        Notification::create([
            'titre' => 'Compte ' . $status,
            'message' => 'Votre compte a été ' . $status . ' par l\'administrateur.',
            'type' => $user->est_actif ? 'success' : 'warning',
            'user_id' => $user->id_user,
        ]);

        return back()->with('success', 'Utilisateur ' . $status . ' avec succès');
    }
}