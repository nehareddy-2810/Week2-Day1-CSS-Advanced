<!DOCTYPE html>
<html>
<head>
    <title>Career Intelligence Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        h1 {
            color: #243b64;
        }

        h2 {
            color: #34495e;
        }

        .score {
            font-size: 42px;
            font-weight: bold;
            color: #2563eb;
        }

        .archetype {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            margin: 8px 0;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .button {
            display: inline-block;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        li {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">
        <h1>Career Intelligence Report</h1>

        <h2>Employability Score</h2>

        <div class="score">
            {{ $report->employability_score }}/100
        </div>
    </div>

    <div class="card">
        <h2>Career Archetype Map</h2>

        @foreach($report->archetype_scores as $name => $value)
            <div class="archetype">
                <strong>{{ $name }}</strong>
                <span>{{ $value }}/100</span>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h2>Career Intelligence</h2>

        <p>{{ $report->narrative }}</p>
    </div>

    <div class="card">
        <h2>Recommendations</h2>

        <ul>
            @foreach($report->recommendations as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>

    @if($report->pdf_path)
        <div class="card">
            <a
                href="{{ route('career-intelligence.download', $report->id) }}"
                class="button"
            >
                Download PDF Report
            </a>
        </div>
    @endif

</div>

</body>
</html>
