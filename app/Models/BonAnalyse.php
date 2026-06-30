<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BonAnalyse extends Model
{
    use HasFactory;

    protected $table = 'bons_analyse';
    protected $primaryKey = 'id_bon';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'numero_bon',
        'id_patient',
        'id_consultation',
        'medecin_id',
        'id_infirmier',
        'bilan_infirmier_id',
        'laborantin_id',
        
        // Informations patient
        'hospitalise',
        'pavillon',
        'chambre',
        'lit',
        'adresse_domicile',
        
        // Informations médecin demandeur
        'nom_medecin_demandeur',
        'prenom_medecin_demandeur',
        'date_demande',
        'heure_demande',
        'service_demandeur',
        'telephone_demandeur',
        
        // Préleveur
        'nom_preleveur',
        'prenom_preleveur',
        'date_prelevement',
        'heure_prelevement',
        'id_client_labo',
        'telephone_preleveur',
        
        // Type d'échantillon
        'type_echantillon',
        'autres_echantillon',
        
        // Examens cochés (stockés en JSON)
        'examens',
        
        // Priorité
        'priorite',
        
        // Statut
        'statut',
        
        // Observations du laborantin
        'observations_laborantin',
        
        // Résultats (JSON)
        'resultats',
        
        // Date de retour
        'date_retour',
        
        // ====== INFORMATIONS INFIRMIER (BILAN) ======
        'motif_infirmier',
        'symptomes_infirmier',
        'observations_infirmier',
        'allergies_infirmier',
        'traitements_infirmier',
        
        // Constantes vitales infirmier
        'poids_infirmier',
        'taille_infirmier',
        'imc_infirmier',
        'temperature_infirmier',
        'tension_arterielle_infirmier',
        'frequence_cardiaque_infirmier',
        'frequence_respiratoire_infirmier',
        'spo2_infirmier',
    ];

    protected $casts = [
        'examens' => 'array',
        'resultats' => 'array',
        'date_demande' => 'date',
        'date_prelevement' => 'date',
        'date_retour' => 'date',
        'hospitalise' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELATIONS ====================
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'id_consultation', 'id_consultation');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id', 'id_user');
    }

    public function infirmier()
    {
        return $this->belongsTo(User::class, 'id_infirmier', 'id_user');
    }

    public function bilanInfirmier()
    {
        return $this->belongsTo(Bilan::class, 'bilan_infirmier_id', 'id_bilan');
    }

    public function laborantin()
    {
        return $this->belongsTo(User::class, 'laborantin_id', 'id_user');
    }

    // ==================== ACCESSORS ====================
    
    public function getTypeEchantillonLabelAttribute()
    {
        $types = [
            'sang' => 'Sang',
            'selles' => 'Selles',
            'urines' => 'Urines',
            'lcr' => 'LCR',
            'crachat' => 'Crachat',
            'autres' => 'Autres'
        ];
        return $types[$this->type_echantillon] ?? 'Non spécifié';
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            'en_attente' => '⏳ En attente',
            'en_cours' => '🔄 En cours',
            'termine' => '✅ Terminé',
            'preleve' => '🧪 Prélevé',
            'analyse' => '🔬 En analyse'
        ];
        return $statuts[$this->statut] ?? 'Inconnu';
    }

    public function getStatutCouleurAttribute()
    {
        $couleurs = [
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'termine' => 'success',
            'preleve' => 'primary',
            'analyse' => 'secondary'
        ];
        return $couleurs[$this->statut] ?? 'secondary';
    }

    public function getPrioriteLabelAttribute()
    {
        $priorites = [
            'normal' => 'Normal',
            'urgent' => '🟥 Urgent'
        ];
        return $priorites[$this->priorite] ?? 'Normal';
    }

    public function getPrioriteCouleurAttribute()
    {
        $couleurs = [
            'normal' => 'primary',
            'urgent' => 'danger'
        ];
        return $couleurs[$this->priorite] ?? 'primary';
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'en_attente' => '<span class="badge bg-warning text-dark">⏳ En attente</span>',
            'en_cours' => '<span class="badge bg-info">🔄 En cours</span>',
            'termine' => '<span class="badge bg-success">✅ Terminé</span>',
            'preleve' => '<span class="badge bg-primary">🧪 Prélevé</span>',
            'analyse' => '<span class="badge bg-secondary">🔬 En analyse</span>'
        ];
        
        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    // ==================== GÉNÉRATION NUMÉRO ====================
    
    public static function generateNumeroBon()
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        $last = self::whereYear('created_at', $year)
            ->orderBy('id_bon', 'desc')
            ->first();
        
        if ($last && $last->numero_bon) {
            $lastNumber = intval(substr($last->numero_bon, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'LS-FLT-' . $year . $month . '-' . $newNumber;
    }

    // ==================== SCOPES ====================
    
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopePreleve($query)
    {
        return $query->where('statut', 'preleve');
    }

    public function scopeAnalyse($query)
    {
        return $query->where('statut', 'analyse');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priorite', 'urgent');
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

    public function scopeParConsultation($query, $consultationId)
    {
        return $query->where('id_consultation', $consultationId);
    }

    // ==================== MÉTHODES UTILES ====================
    
    public function hasBilanInfirmier()
    {
        return $this->id_infirmier !== null && $this->bilan_infirmier_id !== null;
    }

    public function getInfirmierNomCompletAttribute()
    {
        if ($this->infirmier) {
            return $this->infirmier->nom_complet;
        }
        return 'Non assigné';
    }

    public function getBilanInfirmierResumeAttribute()
    {
        $resume = [];
        
        if ($this->poids_infirmier) $resume[] = "Poids: {$this->poids_infirmier} kg";
        if ($this->taille_infirmier) $resume[] = "Taille: {$this->taille_infirmier} cm";
        if ($this->temperature_infirmier) $resume[] = "Temp: {$this->temperature_infirmier}°C";
        if ($this->tension_arterielle_infirmier) $resume[] = "TA: {$this->tension_arterielle_infirmier}";
        if ($this->frequence_cardiaque_infirmier) $resume[] = "FC: {$this->frequence_cardiaque_infirmier} bpm";
        if ($this->frequence_respiratoire_infirmier) $resume[] = "FR: {$this->frequence_respiratoire_infirmier}/min";
        if ($this->spo2_infirmier) $resume[] = "SpO2: {$this->spo2_infirmier}%";
        
        return implode(' | ', $resume);
    }

    public function getExamensListeAttribute()
    {
        $liste = [];
        $examens = $this->examens ?? [];
        
        foreach ($examens as $categorie => $items) {
            foreach ($items as $key => $value) {
                if ($value == 1) {
                    $liste[] = $key;
                }
            }
        }
        
        return $liste;
    }

    public function getNombreExamensAttribute()
    {
        return count($this->examens_liste);
    }
}