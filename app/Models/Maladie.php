<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maladie extends Model
{
    use HasFactory;

    protected $table = 'maladies';
    protected $primaryKey = 'id_maladie';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code_maladie',
        'nom_maladie',
        'description',
        'categorie',
        'symptomes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function examens()
    {
        return $this->hasMany(Examen::class, 'id_maladie', 'id_maladie');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'id_maladie', 'id_maladie');
    }

    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        return "{$this->code_maladie} - {$this->nom_maladie}";
    }
}