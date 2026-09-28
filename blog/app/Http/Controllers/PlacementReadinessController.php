<?php

namespace App\Http\Controllers;

use App\Models\PlacementReadinessScore;
use Illuminate\Http\Request;

class PlacementReadinessController extends Controller
{
    public function show()
    {
        $score = PlacementReadinessScore::first();

        return view('cia-career.readiness', compact('score'));
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'project_completion' => 'required|numeric|min:0|max:100',
            'assessment_scores' => 'required|numeric|min:0|max:100',
            'cia_usage' => 'required|numeric|min:0|max:100',
            'docs_score' => 'required|numeric|min:0|max:100',
            'viva_scores' => 'required|numeric|min:0|max:100',
            'code_review' => 'required|numeric|min:0|max:100',
            'demo_clarity' => 'required|numeric|min:0|max:100',
            'git_quality' => 'required|numeric|min:0|max:100',
        ]);

        $data['user_id'] = auth()->id() ?? 1;

        $data['placement_readiness'] =
            ($data['project_completion'] * 0.40) +
            ($data['assessment_scores'] * 0.30) +
            ($data['cia_usage'] * 0.10) +
            ($data['docs_score'] * 0.20);

        $data['interview_readiness'] =
            ($data['viva_scores'] * 0.40) +
            ($data['code_review'] * 0.30) +
            ($data['demo_clarity'] * 0.20) +
            ($data['git_quality'] * 0.10);

        PlacementReadinessScore::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );

        return redirect()
            ->route('cia-career.readiness')
            ->with('success', 'Readiness scores calculated successfully.');
    }
}