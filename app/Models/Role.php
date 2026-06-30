<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_role';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'nom_role',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id_role');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Obtenir le libellé du rôle avec une icône
     */
    public function getLibelleAvecIconeAttribute()
    {
        $icons = [
            'Gestionnaire système' => '👤',
            'Médecin' => '👨‍⚕️',
            'Réceptionniste' => '🖥️',
            'Laborantin' => '🔬',
            'Infirmier' => '🩺'
        ];
        
        return ($icons[$this->nom_role] ?? '📋') . ' ' . $this->nom_role;
    }

    /**
     * Obtenir la couleur du badge pour le rôle
     */
    public function getBadgeColorAttribute()
    {
        $colors = [
            'Gestionnaire système' => 'danger',
            'Médecin' => 'primary',
            'Réceptionniste' => 'success',
            'Laborantin' => 'info',
            'Infirmier' => 'warning'
        ];
        
        return $colors[$this->nom_role] ?? 'secondary';
    }

    /**
     * Obtenir le badge HTML du rôle
     */
    public function getBadgeAttribute()
    {
        $color = $this->badge_color;
        return '<span class="badge badge-' . $color . '">' . $this->nom_role . '</span>';
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeGestionnaire($query)
    {
        return $query->where('id_role', 1);
    }

    public function scopeMedecin($query)
    {
        return $query->where('id_role', 2);
    }

    public function scopeReceptionniste($query)
    {
        return $query->where('id_role', 3);
    }

    public function scopeLaborantin($query)
    {
        return $query->where('id_role', 4);
    }

    public function scopeInfirmier($query)
    {
        return $query->where('id_role', 5);
    }

    // ============================================================
    // MÉTHODES UTILES
    // ============================================================

    /**
     * Vérifier si le rôle est un gestionnaire
     */
    public function estGestionnaire(): bool
    {
        return $this->id_role === 1;
    }

    /**
     * Vérifier si le rôle est un médecin
     */
    public function estMedecin(): bool
    {
        return $this->id_role === 2;
    }

    /**
     * Vérifier si le rôle est un réceptionniste
     */
    public function estReceptionniste(): bool
    {
        return $this->id_role === 3;
    }

    /**
     * Vérifier si le rôle est un laborantin
     */
    public function estLaborantin(): bool
    {
        return $this->id_role === 4;
    }

    /**
     * Vérifier si le rôle est un infirmier
     */
    public function estInfirmier(): bool
    {
        return $this->id_role === 5;
    }

    /**
     * Obtenir tous les rôles pour les sélecteurs
     */
    public static function getForSelect(): array
    {
        return self::orderBy('id_role')->pluck('nom_role', 'id_role')->toArray();
    }

    /**
     * Obtenir les rôles sauf un
     */
    public static function getRolesSauf($excludeId): array
    {
        return self::where('id_role', '!=', $excludeId)
            ->orderBy('id_role')
            ->pluck('nom_role', 'id_role')
            ->toArray();
    }
}