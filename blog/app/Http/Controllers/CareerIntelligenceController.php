<?php

namespace App\Http\Controllers;

use App\Models\CandidateReport;
use App\Models\PlacementReadinessScore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CareerIntelligenceController extends Controller
{
    public function generate(Request $request)
    {
        $user = $request->user();

        $score = PlacementReadinessScore::where('user_id', $user->id)->first();

        if (!$score) {
            return back()->with(
                'error',
                'Please complete your placement readiness score first.'
            );
        }

        // Six Career Archetypes
        $archetypes = [
            'Builder' => round((
                $score->project_completion +
                $score->code_review +
                $score->git_quality
            ) / 3),

            'Analyst' => round((
                $score->assessment_scores +
                $score->docs_score +
                $score->code_review
            ) / 3),

            'Leader' => round((
                $score->viva_scores +
                $score->demo_clarity +
                $score->interview_readiness
            ) / 3),

            'Innovator' => round((
                $score->cia_usage +
                $score->project_completion +
                $score->demo_clarity
            ) / 3),

            'Specialist' => round((
                $score->assessment_scores +
                $score->code_review +
                $score->viva_scores
            ) / 3),

            'Generalist' => round((
                $score->project_completion +
                $score->docs_score +
                $score->git_quality +
                $score->interview_readiness
            ) / 4),
        ];

        // Overall Employability Score
        $employabilityScore = round(
            (
                $score->placement_readiness +
                $score->interview_readiness
            ) / 2,
            2
        );

        // Default fallback content
        $narrative =
            'Your career profile demonstrates a combination of technical '
            . 'execution, analytical ability and professional readiness. '
            . 'Continue strengthening practical projects and interview skills.';

        $recommendations = [
            'Strengthen the lowest-scoring career archetype.',
            'Continue developing practical full-stack projects.',
            'Practice technical and behavioral interviews regularly.',
        ];

        /*
        |--------------------------------------------------------------------------
        | Groq AI
        |--------------------------------------------------------------------------
        */

        if (env('GROQ_API_KEY')) {

            try {

                $prompt = "
You are a career intelligence assistant.

Employability Score: {$employabilityScore}

Archetype Scores:
Builder: {$archetypes['Builder']}
Analyst: {$archetypes['Analyst']}
Leader: {$archetypes['Leader']}
Innovator: {$archetypes['Innovator']}
Specialist: {$archetypes['Specialist']}
Generalist: {$archetypes['Generalist']}

Provide:
1. A short career intelligence narrative.
2. Exactly three practical career recommendations.

Return JSON only:

{
    \"narrative\": \"short career analysis\",
    \"recommendations\": [
        \"recommendation 1\",
        \"recommendation 2\",
        \"recommendation 3\"
    ]
}
";

                $response = Http::withToken(env('GROQ_API_KEY'))
                    ->timeout(30)
                    ->post(
                        'https://api.groq.com/openai/v1/chat/completions',
                        [
                            'model' => env(
                                'GROQ_MODEL',
                                'llama-3.1-8b-instant'
                            ),

                            'messages' => [
                                [
                                    'role' => 'system',
                                    'content' =>
                                        'You are a professional career '
                                        . 'intelligence assistant. '
                                        . 'Return valid JSON only.',
                                ],
                                [
                                    'role' => 'user',
                                    'content' => $prompt,
                                ],
                            ],

                            'temperature' => 0.4,
                        ]
                    );

                if ($response->successful()) {

                    $content = $response->json(
                        'choices.0.message.content'
                    );

                    $content = trim($content);

                    $content = preg_replace(
                        '/^```json\s*/',
                        '',
                        $content
                    );

                    $content = preg_replace(
                        '/\s*```$/',
                        '',
                        $content
                    );

                    $aiData = json_decode(
                        $content,
                        true
                    );

                    if (is_array($aiData)) {

                        $narrative =
                            $aiData['narrative']
                            ?? $narrative;

                        $recommendations =
                            $aiData['recommendations']
                            ?? $recommendations;
                    }
                }

            } catch (\Throwable $e) {

                // Fallback content is used if Groq fails.
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save Report
        |--------------------------------------------------------------------------
        */

        $report = CandidateReport::create([
            'user_id' => $user->id,
            'archetype_scores' => $archetypes,
            'employability_score' => $employabilityScore,
            'narrative' => $narrative,
            'recommendations' => $recommendations,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        try {

            $pdf = Pdf::loadView(
                'career-intelligence.pdf',
                [
                    'report' => $report,
                ]
            );

            $fileName =
                'career-intelligence-'
                . $user->id
                . '-'
                . $report->id
                . '.pdf';

            Storage::disk('public')->put(
                'career-reports/' . $fileName,
                $pdf->output()
            );

            $report->update([
                'pdf_path' => 'career-reports/' . $fileName,
            ]);

        } catch (\Throwable $e) {

            // Report remains available even if PDF generation fails.
        }

        return redirect()
            ->route(
                'career-intelligence.show',
                $report->id
            )
            ->with(
                'success',
                'Career Intelligence Report generated successfully.'
            );
    }

    public function show(
        Request $request,
        CandidateReport $report
    ) {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        return view(
            'career-intelligence.show',
            compact('report')
        );
    }

    public function download(
        Request $request,
        CandidateReport $report
    ) {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        if (
            !$report->pdf_path ||
            !Storage::disk('public')->exists(
                $report->pdf_path
            )
        ) {
            abort(404, 'PDF report not found.');
        }

        return Storage::disk('public')->download(
            $report->pdf_path,
            'career-intelligence-report.pdf'
        );
    }
}