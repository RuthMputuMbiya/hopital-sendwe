<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bilan extends Model
{
    use HasFactory;

    protected $table = 'bilans';
    protected $primaryKey = 'id_bilan';

    protected $fillable = [
        'consultation_id',
        'infirmier_id',
        'poids',
        'taille',
        'imc',
        'temperature',
        'tension_arterielle',
        'frequence_cardiaque',
        'frequence_respiratoire',
        'spo2',
        'motif',
        'symptomes',
        'observations',
        'allergies',
        'traitements_en_cours',
        'est_transmis',
        'date_transmission',
        'hospitalise',
        'pavillon',
        'chambre',
        'lit'
    ];

    protected $casts = [
        'poids' => 'decimal:2',
        'taille' => 'decimal:2',
        'imc' => 'decimal:2',
        'temperature' => 'decimal:1',
        'frequence_cardiaque' => 'integer',
        'frequence_respiratoire' => 'integer',
        'spo2' => 'integer',
        'est_transmis' => 'boolean',
        'date_transmission' => 'datetime',
        'hospitalise' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================================
    // BOOT
    // ============================================================

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($bilan) {
            $bilan->calculerImc();
        });
        
        static::updating(function ($bilan) {
            $bilan->calculerImc();
        });
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id_consultation');
    }

    public function infirmier()
    {
        return $this->belongsTo(User::class, 'infirmier_id', 'id_user');
    }

    public function patient()
    {
        return $this->hasOneThrough(
            Patient::class,
            Consultation::class,
            'id_consultation',
            'id_patient',
            'consultation_id',
            'id_patient'
        );
    }

    // ============================================================
    // IMC METHODS
    // ============================================================

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

    public function getImcIconAttribute()
    {
        $imc = $this->imc;
        if (!$imc) return 'fa-question-circle';
        
        if ($imc < 18.5) return 'fa-exclamation-triangle';
        if ($imc < 25) return 'fa-check-circle';
        if ($imc < 30) return 'fa-info-circle';
        return 'fa-times-circle';
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getDateFormateeAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-';
    }

    public function getDateTransmissionFormateeAttribute()
    {
        return $this->date_transmission ? Carbon::parse($this->date_transmission)->format('d/m/Y H:i') : 'Non transmis';
    }

    public function getTensionSystoliqueAttribute()
    {
        if ($this->tension_arterielle) {
            $parts = explode('/', $this->tension_arterielle);
            return $parts[0] ?? null;
        }
        return null;
    }

    public function getTensionDiastoliqueAttribute()
    {
        if ($this->tension_arterielle) {
            $parts = explode('/', $this->tension_arterielle);
            return $parts[1] ?? null;
        }
        return null;
    }

    public function getEstNormalAttribute()
    {
        // Vérification basique des constantes vitales
        $normal = true;
        
        if ($this->temperature && ($this->temperature < 36 || $this->temperature > 37.5)) {
            $normal = false;
        }
        
        if ($this->frequence_cardiaque && ($this->frequence_cardiaque < 60 || $this->frequence_cardiaque > 100)) {
            $normal = false;
        }
        
        if ($this->tension_arterielle) {
            $sys = $this->tension_systolique;
            $dia = $this->tension_diastolique;
            if ($sys && ($sys < 90 || $sys > 140)) {
                $normal = false;
            }
            if ($dia && ($dia < 60 || $dia > 90)) {
                $normal = false;
            }
        }
        
        return $normal;
    }

    public function getStatutVitalAttribute()
    {
        return $this->est_normal ? '✅ Stable' : '⚠️ À surveiller';
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeTransmis($query)
    {
        return $query->where('est_transmis', true);
    }

    public function scopeNonTransmis($query)
    {
        return $query->where('est_transmis', false);
    }

    public function scopeParInfirmier($query, $infirmierId)
    {
        return $query->where('infirmier_id', $infirmierId);
    }

    public function scopeParConsultation($query, $consultationId)
    {
        return $query->where('consultation_id', $consultationId);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    // ============================================================
    // MÉTHODES UTILES
    // ============================================================

    public function transmettre()
    {
        $this->update([
            'est_transmis' => true,
            'date_transmission' => now()
        ]);
        
        // Mettre à jour le statut de la consultation
        if ($this->consultation) {
            $this->consultation->updateStatutConsultation('bilan_fait');
        }
        
        return $this;
    }

    public function getResumeAttribute()
    {
        $resume = [];
        
        if ($this->poids) $resume[] = "Poids: {$this->poids} kg";
        if ($this->taille) $resume[] = "Taille: {$this->taille} cm";
        if ($this->temperature) $resume[] = "Temp: {$this->temperature}°C";
        if ($this->tension_arterielle) $resume[] = "TA: {$this->tension_arterielle}";
        if ($this->frequence_cardiaque) $resume[] = "FC: {$this->frequence_cardiaque} bpm";
        if ($this->frequence_respiratoire) $resume[] = "FR: {$this->frequence_respiratoire}/min";
        if ($this->spo2) $resume[] = "SpO2: {$this->spo2}%";
        
        return implode(' | ', $resume);
    }

    // ============================================================
    // STATISTIQUES
    // ============================================================

    public static function getStats($infirmierId = null)
    {
        $query = self::query();
        
        if ($infirmierId) {
            $query->where('infirmier_id', $infirmierId);
        }
        
        return [
            'total' => $query->count(),
            'transmis' => (clone $query)->transmis()->count(),
            'non_transmis' => (clone $query)->nonTransmis()->count(),
            'aujourdhui' => (clone $query)->aujourdhui()->count(),
            'cette_semaine' => (clone $query)->cetteSemaine()->count(),
        ];
    }

    public static function getImcStats($infirmierId = null)
    {
        $query = self::query();
        
        if ($infirmierId) {
            $query->where('infirmier_id', $infirmierId);
        }
        
        return [
            'maigreur' => (clone $query)->whereRaw('poids / POW(taille/100, 2) < 18.5')->count(),
            'normal' => (clone $query)->whereRaw('poids / POW(taille/100, 2) BETWEEN 18.5 AND 24.9')->count(),
            'surpoids' => (clone $query)->whereRaw('poids / POW(taille/100, 2) BETWEEN 25 AND 29.9')->count(),
            'obesite' => (clone $query)->whereRaw('poids / POW(taille/100, 2) >= 30')->count(),
        ];
    }
}