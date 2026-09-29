<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementReadinessScore extends Model
{
    protected $fillable = [
        'user_id',
        'project_completion',
        'assessment_scores',
        'cia_usage',
        'docs_score',
        'viva_scores',
        'code_review',
        'demo_clarity',
        'git_quality',
        'placement_readiness',
        'interview_readiness',
    ];

    protected $casts = [
        'project_completion' => 'float',
        'assessment_scores' => 'float',
        'cia_usage' => 'float',
        'docs_score' => 'float',
        'viva_scores' => 'float',
        'code_review' => 'float',
        'demo_clarity' => 'float',
        'git_quality' => 'float',
        'placement_readiness' => 'float',
        'interview_readiness' => 'float',
    ];
}