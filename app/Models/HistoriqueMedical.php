<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueMedical extends Model
{
    use HasFactory;

    protected $table = 'historique_medical';
    protected $primaryKey = 'id_historique';
    
    protected $fillable = [
        'action',
        'description',
        'table_concernee',
        'id_patient',
        'user_id',
        'anciennes_valeurs',
        'nouvelles_valeurs'
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient', 'id_patient');
    }
}