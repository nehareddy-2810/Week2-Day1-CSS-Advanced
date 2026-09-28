<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;

class InternshipMatchingController extends Controller
{
    public function index()
    {
        $result = Process::run(
            'python ai/chromadb/internship_matcher.py'
        );

        if ($result->failed()) {
            return view('internship-matches', [
                'matches' => [],
                'error' => 'ChromaDB matching service failed.'
            ]);
        }

        $matches = json_decode(
            trim($result->output()),
            true
        );

        return view('internship-matches', [
            'matches' => $matches ?? [],
            'error' => null
        ]);
    }
}