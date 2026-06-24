<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichierPatient extends Model
{
    use HasFactory;

    protected $table = 'fichiers_patients';
    protected $fillable = [
        'id_patient',
        'nom_fichier',
        'chemin_fichier',
        'type_fichier',
        'extension',
        'taille',
        'titre',
        'description',
        'uploaded_by'
    ];

    protected $appends = ['taille_formattee', 'icone'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id_user');
    }

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

    public function getIconeAttribute()
    {
        $icons = [
            'image' => 'fas fa-image',
            'pdf' => 'fas fa-file-pdf',
            'document' => 'fas fa-file-word',
            'radiologie' => 'fas fa-x-ray',
            'default' => 'fas fa-file'
        ];
        return $icons[$this->type_fichier] ?? $icons['default'];
    }
}