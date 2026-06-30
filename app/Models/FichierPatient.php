<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichierPatient extends Model
{
    use HasFactory;

    protected $table = 'fichiers_patients';
    protected $primaryKey = 'id_fichier';  // ✅ AJOUT DE LA CLÉ PRIMAIRE
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_patient',
        'nom_fichier',
        'chemin_fichier',
        'type_fichier',
        'extension',
        'taille',
        'titre',
        'description',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['taille_formattee', 'icone', 'date_formatee'];

    protected $casts = [
        'taille' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id_user');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Formater la taille du fichier (B, KB, MB, GB)
     */
    public function getTailleFormatteeAttribute()
    {
        $bytes = $this->taille;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtenir l'icône du fichier
     */
    public function getIconeAttribute()
    {
        $type = $this->type_fichier ?? 'default';
        
        $icons = [
            'image' => 'fas fa-image',
            'pdf' => 'fas fa-file-pdf',
            'document' => 'fas fa-file-word',
            'excel' => 'fas fa-file-excel',
            'powerpoint' => 'fas fa-file-powerpoint',
            'archive' => 'fas fa-file-archive',
            'video' => 'fas fa-file-video',
            'audio' => 'fas fa-file-audio',
            'texte' => 'fas fa-file-alt',
            'radiologie' => 'fas fa-x-ray',
            'default' => 'fas fa-file'
        ];
        
        return $icons[$type] ?? $icons['default'];
    }

    /**
     * Obtenir la date formatée
     */
    public function getDateFormateeAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : 'Date non définie';
    }

    /**
     * Obtenir la date courte
     */
    public function getDateCourteAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : 'Date non définie';
    }

    /**
     * Obtenir l'extension du fichier en majuscules
     */
    public function getExtensionMajAttribute()
    {
        return strtoupper($this->extension ?? '');
    }

    /**
     * Obtenir la classe CSS pour l'icône
     */
    public function getIconClassAttribute()
    {
        $classes = [
            'image' => 'text-primary',
            'pdf' => 'text-danger',
            'document' => 'text-info',
            'excel' => 'text-success',
            'powerpoint' => 'text-warning',
            'archive' => 'text-secondary',
            'video' => 'text-danger',
            'audio' => 'text-success',
            'texte' => 'text-muted',
            'radiologie' => 'text-info',
            'default' => 'text-secondary'
        ];
        
        $type = $this->type_fichier ?? 'default';
        return $classes[$type] ?? $classes['default'];
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeParPatient($query, $patientId)
    {
        return $query->where('id_patient', $patientId);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_fichier', $type);
    }

    public function scopeParExtension($query, $extension)
    {
        return $query->where('extension', $extension);
    }

    public function scopeParUploader($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeRecents($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============================================================
    // MÉTHODES UTILES
    // ============================================================

    /**
     * Vérifier si le fichier est une image
     */
    public function estImage()
    {
        $images = ['image', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(strtolower($this->extension), $images) || $this->type_fichier === 'image';
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function estPdf()
    {
        return strtolower($this->extension) === 'pdf' || $this->type_fichier === 'pdf';
    }

    /**
     * Vérifier si le fichier est un document Word
     */
    public function estDocument()
    {
        $documents = ['doc', 'docx', 'odt'];
        return in_array(strtolower($this->extension), $documents) || $this->type_fichier === 'document';
    }

    /**
     * Vérifier si le fichier est un Excel
     */
    public function estExcel()
    {
        $excels = ['xls', 'xlsx', 'ods'];
        return in_array(strtolower($this->extension), $excels) || $this->type_fichier === 'excel';
    }

    /**
     * Obtenir le chemin complet du fichier
     */
    public function getCheminCompletAttribute()
    {
        if ($this->chemin_fichier) {
            return asset('storage/' . $this->chemin_fichier);
        }
        return null;
    }

    /**
     * Obtenir le nom complet du fichier avec extension
     */
    public function getNomCompletAttribute()
    {
        return $this->nom_fichier . '.' . $this->extension;
    }
}