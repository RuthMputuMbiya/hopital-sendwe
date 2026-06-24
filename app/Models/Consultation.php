<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
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
        'id_patient',
        'medecin_id',
        'id_maladie',
        'statut'
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
        'frequence_cardiaque' => 'integer'
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

    public function maladie()
    {
        return $this->belongsTo(Maladie::class, 'id_maladie', 'id_maladie');
    }

    public function examens()
    {
        return $this->hasMany(Examen::class, 'consultation_id', 'id_consultation');
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
        
        if ($imc < 16.5) return 'Famine';
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

    public function scopeParPatient($query, $patientId)
    {
        return $query->where('id_patient', $patientId);
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
}