<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Patient extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'patients';
    protected $primaryKey = 'id_patient';

    protected $fillable = [
        'numero_fiche',
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'profession',
        'situation_matrimoniale',
        'telephone',
        'telephone_secondaire',
        'email',
        'adresse',
        'ville',
        'quartier',
        // 'groupe_sanguin',  // ❌ SUPPRIMÉ
        'antecedents_medicaux',
        'antecedents_chirurgicaux',
        'antecedents_familiaux',
        'allergies',
        'traitements_en_cours',
        'vaccinations',
        'handicap',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'contact_urgence_relation',
        'created_by',
        'password',
        'is_active',
        'first_login',
        'password_changed_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'is_active' => 'boolean',
        'first_login' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getNomCompletCompletAttribute()
    {
        return $this->prenom . ' ' . $this->postnom . ' ' . $this->nom;
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    // ============================================================
    // PREMIÈRE CONNEXION - CHANGEMENT MOT DE PASSE
    // ============================================================

    public function needsPasswordChange()
    {
        return $this->first_login || $this->password_changed_at === null;
    }

    public function markPasswordAsChanged()
    {
        $this->update([
            'first_login' => false,
            'password_changed_at' => now()
        ]);
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'id_patient', 'id_patient');
    }

    // ✅ RELATION AVEC LES BONS D'ANALYSE
    public function bonsAnalyse()
    {
        return $this->hasMany(BonAnalyse::class, 'id_patient', 'id_patient');
    }

    public function fichiers()
    {
        return $this->hasMany(FichierPatient::class, 'id_patient', 'id_patient');
    }

    // ✅ RELATION AVEC LES EXAMENS
    public function examens()
    {
        return $this->hasMany(Examen::class, 'id_patient', 'id_patient');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where(function($q) use ($term) {
                $q->where('nom', 'LIKE', "%{$term}%")
                  ->orWhere('postnom', 'LIKE', "%{$term}%")
                  ->orWhere('prenom', 'LIKE', "%{$term}%")
                  ->orWhere('numero_fiche', 'LIKE', "%{$term}%")
                  ->orWhere('telephone', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }
        return $query;
    }

    public function scopeConsultes($query, $medecinId)
    {
        return $query->whereHas('consultations', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        });
    }

    public function scopeNonConsultes($query, $medecinId)
    {
        return $query->whereDoesntHave('consultations', function($q) use ($medecinId) {
            $q->where('medecin_id', $medecinId);
        });
    }

    // ============================================================
    // STATISTIQUES
    // ============================================================

    public static function getStatsForMedecin($medecinId)
    {
        $total = self::count();
        $consultes = self::consultes($medecinId)->count();
        $nonConsultes = self::nonConsultes($medecinId)->count();
        $moisActuel = self::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $total,
            'consultes' => $consultes,
            'non_consultes' => $nonConsultes,
            'mois_actuel' => $moisActuel,
        ];
    }
}