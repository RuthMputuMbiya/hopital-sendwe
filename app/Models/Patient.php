<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

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
        return $this->prenom . ' ' . $this->nom . ' ' . $this->postnom;
    }

    public function getNomCompletCourtAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * ✅ CALCUL DE L'ÂGE - CORRIGÉ
     * Retourne l'âge en années ou null si pas de date de naissance
     */
    public function getAgeAttribute()
    {
        if ($this->date_naissance) {
            return Carbon::parse($this->date_naissance)->age;
        }
        return null;
    }

    // ✅ STATUT DU PATIENT
    public function getStatutAttribute()
    {
        $derniereConsultation = $this->consultations()->latest()->first();
        
        if (!$derniereConsultation) {
            return 'nouveau';
        }
        
        return $derniereConsultation->statut_consultation ?? 'en_attente_infirmier';
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            'nouveau' => '🆕 Nouveau patient',
            'en_attente_infirmier' => '⏳ En attente infirmier',
            'bilan_fait' => '📋 Bilan fait',
            'en_consultation' => '🩺 En consultation',
            'termine' => '✅ Terminé',
            'annule' => '❌ Annulé'
        ];
        
        return $statuts[$this->statut] ?? 'Inconnu';
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'nouveau' => '<span class="badge bg-primary">🆕 Nouveau</span>',
            'en_attente_infirmier' => '<span class="badge bg-warning text-dark">⏳ En attente infirmier</span>',
            'bilan_fait' => '<span class="badge bg-info">📋 Bilan fait</span>',
            'en_consultation' => '<span class="badge bg-primary">🩺 En consultation</span>',
            'termine' => '<span class="badge bg-success">✅ Terminé</span>',
            'annule' => '<span class="badge bg-danger">❌ Annulé</span>'
        ];
        
        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
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

    public function bonsAnalyse()
    {
        return $this->hasMany(BonAnalyse::class, 'id_patient', 'id_patient');
    }

    public function fichiers()
    {
        return $this->hasMany(FichierPatient::class, 'id_patient', 'id_patient');
    }

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

    public function scopeEnAttenteInfirmier($query)
    {
        return $query->whereHas('consultations', function($q) {
            $q->where('statut_consultation', 'en_attente_infirmier');
        });
    }

    public function scopeBilanFait($query)
    {
        return $query->whereHas('consultations', function($q) {
            $q->where('statut_consultation', 'bilan_fait');
        });
    }

    public function scopeEnConsultation($query)
    {
        return $query->whereHas('consultations', function($q) {
            $q->where('statut_consultation', 'en_consultation');
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

    public static function getStatsForInfirmier()
    {
        return [
            'en_attente' => self::enAttenteInfirmier()->count(),
            'bilan_fait' => self::bilanFait()->count(),
            'en_consultation' => self::enConsultation()->count(),
        ];
    }
}