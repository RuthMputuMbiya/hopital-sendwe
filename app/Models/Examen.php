<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $table = 'examens';
    protected $primaryKey = 'id_examen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'numero_examen',
        'type_examen',
        'resultat',
        'date_examen',
        'heure_examen',
        'fichier_resultat',
        'id_patient',
        'id_maladie',
        'laborantin_id',
        'statut',
        'commentaires',
        'priorite'
    ];

    protected $casts = [
        'date_examen' => 'date',
        'heure_examen' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELATIONS ====================
    
    /**
     * Relation avec le patient
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    /**
     * Relation avec la maladie
     */
    public function maladie()
    {
        return $this->belongsTo(Maladie::class, 'id_maladie', 'id_maladie');
    }

    /**
     * Relation avec le laborantin (utilisateur)
     */
    public function laborantin()
    {
        return $this->belongsTo(User::class, 'laborantin_id', 'id_user');
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Obtenir le libellé du statut
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la couleur du badge du statut
     */
    public function getStatutCouleurAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'termine' => 'success',
            'annule' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Vérifier si le résultat est disponible
     */
    public function aResultat(): bool
    {
        return !is_null($this->resultat) && $this->resultat !== '';
    }

    /**
     * Obtenir la priorité libellée
     */
    public function getPrioriteLabelAttribute(): string
    {
        return match($this->priorite) {
            'urgent' => 'Urgent',
            'normal' => 'Normal',
            default => 'Normal'
        };
    }
}