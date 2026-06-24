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
        'medecin_id',
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
        'type_echantillon', // sang, selles, urines, lcr, crachat, autres
        'autres_echantillon',
        
        // Examens cochés (stockés en JSON)
        'examens',
        
        // Priorité
        'priorite', // normal, urgent
        
        // Statut
        'statut', // en_attente, en_cours, termine
        
        // Observations du laborantin
        'observations_laborantin',
        
        // Résultats (JSON)
        'resultats',
        
        // Date de retour
        'date_retour',
    ];

    protected $casts = [
        'examens' => 'array',
        'resultats' => 'array',
        'date_demande' => 'date',
        'date_prelevement' => 'date',
        'date_retour' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id', 'id_user');
    }

    public function laborantin()
    {
        return $this->belongsTo(User::class, 'laborantin_id', 'id_user');
    }

    // Accesseurs
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
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé'
        ];
        return $statuts[$this->statut] ?? 'Inconnu';
    }

    public function getStatutCouleurAttribute()
    {
        $couleurs = [
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'termine' => 'success'
        ];
        return $couleurs[$this->statut] ?? 'secondary';
    }

    public function getPrioriteLabelAttribute()
    {
        $priorites = [
            'normal' => 'Normal',
            'urgent' => 'Urgent'
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

    // Génération du numéro de bon
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
}