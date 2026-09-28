<!DOCTYPE html>
<html>
<head>
    <title>Internship Matches</title>

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
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px #ddd;
        }

        .score {
            font-size: 25px;
            font-weight: bold;
        }

        .matched {
            color: green;
        }

        .gap {
            color: red;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Career Intelligence – Internship Matches</h1>

    <p>Top 5 internship roles based on your skill profile.</p>

    @if($error)
        <div class="card">
            {{ $error }}
        </div>
    @endif

    @foreach($matches as $match)

        <div class="card">

            <h2>{{ $match['role'] }}</h2>

            <div class="score">
                Match: {{ $match['match_percentage'] }}%
            </div>

            <p class="matched">
                <strong>Matched Skills:</strong>
                {{ implode(', ', $match['matched_skills']) ?: 'None' }}
            </p>

            <p class="gap">
                <strong>Skill Gap:</strong>
                {{ implode(', ', $match['skill_gap']) ?: 'No major gap' }}
            </p>

        </div>

    @endforeach

</div>

</body>
</html>