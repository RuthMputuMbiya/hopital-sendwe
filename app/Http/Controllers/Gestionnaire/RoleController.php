<?php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Liste des rôles
     */
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id_role')->paginate(15);
        
        // Statistiques des utilisateurs par rôle
        $stats = [
            'total_roles' => Role::count(),
            'total_users' => User::count(),
            'system_roles' => Role::whereIn('id_role', [1,2,3,4])->count(),
            'custom_roles' => Role::whereNotIn('id_role', [1,2,3,4])->count(),
            'users_by_role' => [
                'gestionnaires' => User::where('role_id', 1)->count(),
                'medecins' => User::where('role_id', 2)->count(),
                'receptionnistes' => User::where('role_id', 3)->count(),
                'laborantins' => User::where('role_id', 4)->count(),
            ]
        ];
        
        return view('gestionnaire.roles.index', compact('roles', 'stats'));
    }

    /**
     * Formulaire de création d'un rôle
     */
    public function create()
    {
        return view('gestionnaire.roles.create');
    }

    /**
     * Enregistrer un nouveau rôle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_role' => 'required|string|max:50|unique:roles,nom_role',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'nom_role' => $validated['nom_role'],
                'description' => $validated['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Notifier les admins
            $admins = User::where('role_id', 1)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'titre' => '🆕 Nouveau rôle créé',
                    'message' => "Le rôle '{$role->nom_role}' a été créé par " . auth()->user()->nom_complet,
                    'type' => 'success',
                    'user_id' => $admin->id_user,
                    'lien' => route('gestionnaire.roles.show', $role->id_role),
                ]);
            }

            DB::commit();

            return redirect()->route('gestionnaire.roles.index')
                ->with('success', 'Rôle créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création du rôle.')->withInput();
        }
    }

    /**
     * Afficher les détails d'un rôle
     */
    public function show($id)
    {
        $role = Role::withCount('users')->findOrFail($id);
        $users = $role->users()->paginate(10);
        
        // Vérifier si c'est un rôle système
        $systemRoles = [1, 2, 3, 4];
        $isSystemRole = in_array($role->id_role, $systemRoles);
        
        return view('gestionnaire.roles.show', compact('role', 'users', 'isSystemRole'));
    }

    /**
     * Formulaire d'édition d'un rôle
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la modification des rôles système
        $systemRoles = [1, 2, 3, 4];
        if (in_array($role->id_role, $systemRoles)) {
            return redirect()->route('gestionnaire.roles.index')
                ->with('error', 'Les rôles système ne peuvent pas être modifiés.');
        }
        
        return view('gestionnaire.roles.edit', compact('role'));
    }

    /**
     * Mettre à jour un rôle
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la modification des rôles système
        $systemRoles = [1, 2, 3, 4];
        if (in_array($role->id_role, $systemRoles)) {
            return back()->with('error', 'Les rôles système ne peuvent pas être modifiés.');
        }
        
        $validated = $request->validate([
            'nom_role' => ['required', 'string', 'max:50', Rule::unique('roles', 'nom_role')->ignore($role->id_role, 'id_role')],
            'description' => 'nullable|string|max:500',
        ]);

        $role->update($validated);

        return redirect()->route('gestionnaire.roles.index')
            ->with('success', 'Rôle modifié avec succès !');
    }

    /**
     * Supprimer un rôle
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la suppression des rôles système
        $systemRoles = [1, 2, 3, 4];
        if (in_array($role->id_role, $systemRoles)) {
            return back()->with('error', 'Les rôles système ne peuvent pas être supprimés.');
        }
        
        // Vérifier si des utilisateurs ont ce rôle
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce rôle car ' . $role->users()->count() . ' utilisateur(s) y sont associés.');
        }
        
        $role->delete();
        
        return redirect()->route('gestionnaire.roles.index')
            ->with('success', 'Rôle supprimé avec succès !');
    }
}