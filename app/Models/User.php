<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Le nom de la table associée au modèle.
     */
    protected $table = 'users';
    
    /**
     * La clé primaire de la table.
     */
    protected $primaryKey = 'id_user';
    
    /**
     * Indique si la clé primaire est auto-incrémentée.
     */
    public $incrementing = true;
    
    /**
     * Le type de la clé primaire.
     */
    protected $keyType = 'int';

    /**
     * Les attributs qui sont mass assignable.
     */
    protected $fillable = [
        'matricule',
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'email',
        'telephone',
        'adresse',
        'password',
        'role_id',
        'est_actif',
        'date_embauche',
        'specialite',
        'first_login',
        'password_changed_at'
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'est_actif' => 'boolean',
        'first_login' => 'boolean',
        'date_embauche' => 'date',
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Les attributs par défaut.
     */
    protected $attributes = [
        'est_actif' => true,
        'first_login' => true,
    ];

    // ============================================================
    //  RELATIONS
    // ============================================================
    
    /**
     * Relation avec le rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    /**
     * Relation avec les examens effectués par le laborantin.
     */
    public function examensEffectues()
    {
        return $this->hasMany(Examen::class, 'laborantin_id', 'id_user');
    }

    /**
     * Relation avec les consultations effectuées par le médecin.
     */
    public function consultationsEffectuees()
    {
        return $this->hasMany(Consultation::class, 'medecin_id', 'id_user');
    }

    /**
     * Relation avec les notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id_user');
    }

    /**
     * Relation avec l'historique médical.
     */
    public function historiqueActions()
    {
        return $this->hasMany(HistoriqueMedical::class, 'user_id', 'id_user');
    }

    // ============================================================
    //  ACCESSORS
    // ============================================================
    
    /**
     * Obtenir le nom complet (Nom Postnom Prénom)
     */
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->nom} {$this->postnom} {$this->prenom}");
    }

    /**
     * Obtenir le nom abrégé (Prénom Nom)
     */
    public function getNomAbregeAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Obtenir le nom pour l'affichage (Prénom NOM)
     */
    public function getNomAffichageAttribute(): string
    {
        return trim("{$this->prenom} " . strtoupper($this->nom));
    }

    // ============================================================
    //  PREMIÈRE CONNEXION - CHANGEMENT MOT DE PASSE
    // ============================================================
    
    /**
     * Vérifier si l'utilisateur doit changer son mot de passe.
     */
    public function needsPasswordChange(): bool
    {
        return $this->first_login || $this->password_changed_at === null;
    }

    /**
     * Marquer que le mot de passe a été changé.
     */
    public function markPasswordAsChanged(): void
    {
        $this->update([
            'first_login' => false,
            'password_changed_at' => now()
        ]);
    }

    // ============================================================
    //  VÉRIFICATIONS DES RÔLES
    // ============================================================
    
    /**
     * Vérifier si l'utilisateur est un gestionnaire (admin).
     */
    public function estGestionnaire(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Vérifier si l'utilisateur est un médecin.
     */
    public function estMedecin(): bool
    {
        return $this->role_id === 2;
    }

    /**
     * Vérifier si l'utilisateur est un réceptionniste.
     */
    public function estReceptionniste(): bool
    {
        return $this->role_id === 3;
    }

    /**
     * Vérifier si l'utilisateur est un laborantin.
     */
    public function estLaborantin(): bool
    {
        return $this->role_id === 4;
    }

    /**
     * ✅ Vérifier si l'utilisateur est un infirmier.
     */
    public function estInfirmier(): bool
    {
        return $this->role_id === 5;
    }

    /**
     * Vérifier si le compte est actif.
     */
    public function estActif(): bool
    {
        return $this->est_actif;
    }

    // ============================================================
    //  MÉTHODES UTILITAIRES
    // ============================================================
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique.
     */
    public function hasRole(int $roleId): bool
    {
        return $this->role_id === $roleId;
    }

    /**
     * Vérifier si l'utilisateur a l'un des rôles spécifiés.
     */
    public function hasAnyRole(array $roleIds): bool
    {
        return in_array($this->role_id, $roleIds);
    }

    /**
     * Obtenir le libellé du rôle.
     */
    public function getRoleLabelAttribute(): string
    {
        $roles = [
            1 => 'Gestionnaire système',
            2 => 'Médecin',
            3 => 'Réceptionniste',
            4 => 'Laborantin',
            5 => 'Infirmier',  // ✅ AJOUTÉ
        ];
        
        return $roles[$this->role_id] ?? 'Inconnu';
    }

    /**
     * Obtenir la couleur du badge du rôle.
     */
    public function getRoleBadgeColorAttribute(): string
    {
        $colors = [
            1 => 'danger',
            2 => 'primary',
            3 => 'success',
            4 => 'info',
            5 => 'warning',  // ✅ AJOUTÉ - Couleur jaune pour l'infirmier
        ];
        
        return $colors[$this->role_id] ?? 'secondary';
    }

    /**
     * Activer le compte utilisateur.
     */
    public function activer(): void
    {
        $this->update(['est_actif' => true]);
    }

    /**
     * Désactiver le compte utilisateur.
     */
    public function desactiver(): void
    {
        $this->update(['est_actif' => false]);
    }

    // ============================================================
    //  MATRICULE - GÉNÉRATION AUTOMATIQUE
    // ============================================================
    
    /**
     * Récupérer le rôle à partir du matricule.
     */
    public function getRoleFromMatricule(): ?string
    {
        if (empty($this->matricule)) return null;
        
        $prefix = substr($this->matricule, 0, 3);
        
        $roles = [
            'ADM' => 'gestionnaire',
            'DOC' => 'medecin',
            'LAB' => 'laborantin',
            'REC' => 'receptionniste',
            'INF' => 'infirmier',  // ✅ AJOUTÉ
        ];
        
        return $roles[$prefix] ?? null;
    }

    /**
     * Générer un matricule unique.
     */
    public static function generateMatricule(string $role): string
    {
        $prefixes = [
            'gestionnaire' => 'ADM',
            'medecin' => 'DOC',
            'laborantin' => 'LAB',
            'receptionniste' => 'REC',
            'infirmier' => 'INF',  // ✅ AJOUTÉ
        ];
        
        $prefix = $prefixes[$role] ?? 'USR';
        
        $last = self::where('matricule', 'LIKE', $prefix . '%')
            ->orderBy('matricule', 'desc')
            ->first();
            
        if ($last) {
            $number = intval(substr($last->matricule, 3)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le dashboard route selon le rôle.
     */
    public function getDashboardRoute(): string
    {
        $role = $this->getRoleFromMatricule();
        
        $dashboards = [
            'gestionnaire' => 'gestionnaire.dashboard',
            'medecin' => 'medecin.dashboard',
            'laborantin' => 'laborantin.dashboard',
            'receptionniste' => 'receptionniste.dashboard',
            'infirmier' => 'infirmier.dashboard',  // ✅ AJOUTÉ
        ];
        
        return $dashboards[$role] ?? 'dashboard';
    }
}