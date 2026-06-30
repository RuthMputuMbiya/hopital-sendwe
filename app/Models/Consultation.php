<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';
    protected $primaryKey = 'id_consultation';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'numero_consultation',
        'date_consultation',
        'heure_consultation',
        'motif',
        'symptomes',
        'diagnostic',
        'prescription',
        'observations_medecin',
        'tension_arterielle',
        'poids',
        'taille',
        'imc',
        'temperature',
        'frequence_cardiaque',
        'frequence_respiratoire',
        'spo2',
        'id_patient',
        'medecin_id',
        'id_infirmier',
        'id_maladie',
        'statut',
        'statut_consultation',
        'hospitalise',
        'pavillon',
        'chambre',
        'lit',
        'adresse_domicile',
        'moyen',
        'priorite',
        'observations_cliniques'
    ];

    protected $casts = [
        'date_consultation' => 'date',
        'heure_consultation' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'poids' => 'decimal:2',
        'taille' => 'decimal:2',
        'imc' => 'decimal:2',
        'temperature' => 'decimal:1',
        'frequence_cardiaque' => 'integer',
        'frequence_respiratoire' => 'integer',
        'spo2' => 'integer',
        'hospitalise' => 'boolean',
        'priorite' => 'string',
    ];

    // ==================== BOOT ====================
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($consultation) {
            if (empty($consultation->numero_consultation)) {
                $consultation->numero_consultation = self::generateNumeroConsultation();
            }
            $consultation->calculerImc();
            
            if (empty($consultation->statut_consultation)) {
                $consultation->statut_consultation = 'en_attente_infirmier';
            }
            if (empty($consultation->statut)) {
                $consultation->statut = 'en_cours';
            }
        });
        
        static::updating(function ($consultation) {
            $consultation->calculerImc();
        });
    }

    // ==================== RELATIONS ====================
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id', 'id_user');
    }

    public function infirmier()
    {
        return $this->belongsTo(User::class, 'id_infirmier', 'id_user');
    }

    public function maladie()
    {
        return $this->belongsTo(Maladie::class, 'id_maladie', 'id_maladie');
    }

    public function examens()
    {
        return $this->hasMany(Examen::class, 'consultation_id', 'id_consultation');
    }

    public function bilan()
    {
        return $this->hasOne(Bilan::class, 'consultation_id', 'id_consultation');
    }

    public function bonsAnalyse()
    {
        return $this->hasMany(BonAnalyse::class, 'id_consultation', 'id_consultation');
    }

    // ==================== ACCESSORS ====================
    
    public function getDateConsultationFormateeAttribute()
    {
        if ($this->date_consultation) {
            return Carbon::parse($this->date_consultation)->format('d/m/Y');
        }
        return 'Date non définie';
    }

    public function getHeureConsultationFormateeAttribute()
    {
        if ($this->heure_consultation) {
            return Carbon::parse($this->heure_consultation)->format('H:i');
        }
        return '--:--';
    }

    public function getDateTimeConsultationAttribute()
    {
        if ($this->date_consultation && $this->heure_consultation) {
            return Carbon::parse($this->date_consultation . ' ' . $this->heure_consultation)->format('d/m/Y H:i');
        }
        return 'Date non définie';
    }

    // ✅ ACCESSORS POUR LE STATUT DE CONSULTATION
    public function getStatutConsultationLabelAttribute()
    {
        $labels = [
            'en_attente_infirmier' => '⏳ En attente infirmier',
            'bilan_fait' => '📋 Bilan fait',
            'en_consultation' => '🩺 En consultation',
            'termine' => '✅ Terminé',
            'annule' => '❌ Annulé'
        ];
        
        return $labels[$this->statut_consultation] ?? 'Inconnu';
    }

    public function getStatutConsultationBadgeAttribute()
    {
        $badges = [
            'en_attente_infirmier' => '<span class="badge bg-warning text-dark">⏳ En attente infirmier</span>',
            'bilan_fait' => '<span class="badge bg-info">📋 Bilan fait</span>',
            'en_consultation' => '<span class="badge bg-primary">🩺 En consultation</span>',
            'termine' => '<span class="badge bg-success">✅ Terminé</span>',
            'annule' => '<span class="badge bg-danger">❌ Annulé</span>'
        ];
        
        return $badges[$this->statut_consultation] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getStatutConsultationColorAttribute()
    {
        $colors = [
            'en_attente_infirmier' => 'warning',
            'bilan_fait' => 'info',
            'en_consultation' => 'primary',
            'termine' => 'success',
            'annule' => 'danger'
        ];
        
        return $colors[$this->statut_consultation] ?? 'secondary';
    }

    // ANCIEN STATUT (conservé pour compatibilité)
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function getStatutCouleurAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'warning',
            'termine' => 'success',
            'annule' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatutBadgeAttribute()
    {
        $labels = [
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];
        
        $colors = [
            'en_cours' => 'bg-warning text-dark',
            'termine' => 'bg-success',
            'annule' => 'bg-danger'
        ];
        
        $label = $labels[$this->statut] ?? 'Inconnu';
        $color = $colors[$this->statut] ?? 'bg-secondary';
        
        return '<span class="badge ' . $color . '">' . $label . '</span>';
    }

    // ==================== IMC METHODS ====================
    
    public function calculerImc()
    {
        if ($this->poids && $this->taille && $this->taille > 0) {
            $tailleEnMetres = $this->taille / 100;
            $imc = $this->poids / ($tailleEnMetres * $tailleEnMetres);
            $this->attributes['imc'] = round($imc, 2);
        } else {
            $this->attributes['imc'] = null;
        }
        return $this;
    }

    public function getImcAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        if ($this->poids && $this->taille && $this->taille > 0) {
            $tailleEnMetres = $this->taille / 100;
            return round($this->poids / ($tailleEnMetres * $tailleEnMetres), 2);
        }
        return null;
    }

    public function getImcStatusAttribute()
    {
        $imc = $this->imc;
        if (!$imc) return 'Non disponible';
        
        if ($imc < 16.5) return 'Famine sévère';
        if ($imc < 18.5) return 'Maigreur';
        if ($imc < 25) return 'Normal';
        if ($imc < 30) return 'Surpoids';
        if ($imc < 35) return 'Obésité modérée';
        if ($imc < 40) return 'Obésité sévère';
        return 'Obésité morbide';
    }

    public function getImcColorAttribute()
    {
        $imc = $this->imc;
        if (!$imc) return 'secondary';
        
        if ($imc < 18.5) return 'warning';
        if ($imc < 25) return 'success';
        if ($imc < 30) return 'info';
        return 'danger';
    }

    // ==================== SCOPES ====================
    
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeAnnule($query)
    {
        return $query->where('statut', 'annule');
    }

    public function scopeEnAttenteInfirmier($query)
    {
        return $query->where('statut_consultation', 'en_attente_infirmier');
    }

    public function scopeBilanFait($query)
    {
        return $query->where('statut_consultation', 'bilan_fait');
    }

    public function scopeEnConsultation($query)
    {
        return $query->where('statut_consultation', 'en_consultation');
    }

    public function scopeTermineConsultation($query)
    {
        return $query->where('statut_consultation', 'termine');
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_consultation', [$debut, $fin]);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_consultation', Carbon::today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_consultation', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeCeMois($query)
    {
        return $query->whereMonth('date_consultation', Carbon::now()->month)
                     ->whereYear('date_consultation', Carbon::now()->year);
    }

    public function scopeParMedecin($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId);
    }

    public function scopeParInfirmier($query, $infirmierId)
    {
        return $query->where('id_infirmier', $infirmierId);
    }

    public function scopeParPatient($query, $patientId)
    {
        return $query->where('id_patient', $patientId);
    }

    public function scopeAvecBilan($query)
    {
        return $query->has('bilan');
    }

    public function scopeSansBilan($query)
    {
        return $query->doesntHave('bilan');
    }

    // ==================== MUTATORS ====================
    
    public function setDateConsultationAttribute($value)
    {
        if ($value) {
            $this->attributes['date_consultation'] = Carbon::parse($value)->format('Y-m-d');
        } else {
            $this->attributes['date_consultation'] = null;
        }
    }

    public function setHeureConsultationAttribute($value)
    {
        if ($value) {
            $this->attributes['heure_consultation'] = Carbon::parse($value)->format('H:i:s');
        } else {
            $this->attributes['heure_consultation'] = null;
        }
    }

    public function setPoidsAttribute($value)
    {
        $this->attributes['poids'] = $value;
        $this->calculerImc();
    }

    public function setTailleAttribute($value)
    {
        $this->attributes['taille'] = $value;
        $this->calculerImc();
    }

    // ==================== MÉTHODES UTILES ====================
    
    public function updateStatut($statut)
    {
        $this->update(['statut' => $statut]);
        return $this;
    }

    public function updateStatutConsultation($statut)
    {
        $this->update(['statut_consultation' => $statut]);
        return $this;
    }

    public function transmettreAuMedecin()
    {
        return $this->updateStatutConsultation('bilan_fait');
    }

    public function commencerConsultation()
    {
        return $this->updateStatutConsultation('en_consultation');
    }

    public function terminerConsultation()
    {
        return $this->updateStatutConsultation('termine');
    }

    public function annulerConsultation()
    {
        return $this->updateStatutConsultation('annule');
    }

    public function terminer()
    {
        return $this->updateStatut('termine');
    }

    public function annuler()
    {
        return $this->updateStatut('annule');
    }

    public function estTermine()
    {
        return $this->statut === 'termine';
    }

    public function estEnCours()
    {
        return $this->statut === 'en_cours';
    }

    public function estAnnule()
    {
        return $this->statut === 'annule';
    }

    public function estEnAttenteInfirmier()
    {
        return $this->statut_consultation === 'en_attente_infirmier';
    }

    public function estBilanFait()
    {
        return $this->statut_consultation === 'bilan_fait';
    }

    public function estEnConsultation()
    {
        return $this->statut_consultation === 'en_consultation';
    }

    public function estTermineConsultation()
    {
        return $this->statut_consultation === 'termine';
    }

    public function aBilan()
    {
        return $this->bilan()->exists();
    }

    // ==================== STATISTIQUES ====================
    
    public static function getStats($medecinId = null)
    {
        $query = self::query();
        
        if ($medecinId) {
            $query->where('medecin_id', $medecinId);
        }
        
        return [
            'total' => $query->count(),
            'en_cours' => (clone $query)->enCours()->count(),
            'termine' => (clone $query)->termine()->count(),
            'annule' => (clone $query)->annule()->count(),
            'aujourdhui' => (clone $query)->aujourdhui()->count(),
            'cette_semaine' => (clone $query)->cetteSemaine()->count(),
            'ce_mois' => (clone $query)->ceMois()->count(),
        ];
    }

    public static function getStatsInfirmier($infirmierId = null)
    {
        $query = self::query();
        
        if ($infirmierId) {
            $query->where('id_infirmier', $infirmierId);
        }
        
        return [
            'total' => $query->count(),
            'en_attente_infirmier' => (clone $query)->enAttenteInfirmier()->count(),
            'bilan_fait' => (clone $query)->bilanFait()->count(),
            'en_consultation' => (clone $query)->enConsultation()->count(),
            'termine' => (clone $query)->termineConsultation()->count(),
            'annule' => (clone $query)->annule()->count(),
            'aujourdhui' => (clone $query)->aujourdhui()->count(),
            'avec_bilan' => (clone $query)->avecBilan()->count(),
            'sans_bilan' => (clone $query)->sansBilan()->count(),
        ];
    }

    public static function getImcStats($medecinId = null)
    {
        $query = self::query();
        
        if ($medecinId) {
            $query->where('medecin_id', $medecinId);
        }
        
        return [
            'maigreur' => (clone $query)->whereRaw('poids / POW(taille/100, 2) < 18.5')->count(),
            'normal' => (clone $query)->whereRaw('poids / POW(taille/100, 2) BETWEEN 18.5 AND 24.9')->count(),
            'surpoids' => (clone $query)->whereRaw('poids / POW(taille/100, 2) BETWEEN 25 AND 29.9')->count(),
            'obesite' => (clone $query)->whereRaw('poids / POW(taille/100, 2) >= 30')->count(),
        ];
    }

    // ==================== GÉNÉRATION NUMÉRO ====================
    
    public static function generateNumeroConsultation()
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        $lastConsultation = self::whereYear('created_at', $year)
            ->orderBy('id_consultation', 'desc')
            ->first();
        
        if ($lastConsultation && $lastConsultation->numero_consultation) {
            $lastNumber = intval(substr($lastConsultation->numero_consultation, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'CONS-' . $year . $month . '-' . $newNumber;
    }

    // ==================== RECHERCHE ====================
    
    public static function search($term)
    {
        return self::where('numero_consultation', 'LIKE', "%{$term}%")
            ->orWhere('motif', 'LIKE', "%{$term}%")
            ->orWhere('diagnostic', 'LIKE', "%{$term}%")
            ->orWhereHas('patient', function($query) use ($term) {
                $query->where('nom', 'LIKE', "%{$term}%")
                    ->orWhere('prenom', 'LIKE', "%{$term}%")
                    ->orWhere('postnom', 'LIKE', "%{$term}%")
                    ->orWhere('numero_fiche', 'LIKE', "%{$term}%");
            });
    }
}