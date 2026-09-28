<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateReport extends Model
{
    protected $fillable = [
        'user_id',
        'archetype_scores',
        'employability_score',
        'narrative',
        'recommendations',
        'pdf_path',
    ];

    protected $casts = [
        'archetype_scores' => 'array',
        'recommendations' => 'array',
        'employability_score' => 'float',
    ];
}