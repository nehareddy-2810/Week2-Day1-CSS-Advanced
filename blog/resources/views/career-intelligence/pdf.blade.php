<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 25px;
        }

        .score {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

<h1>Career Intelligence Report</h1>

<h2>Employability Score</h2>

<div class="score">
    {{ $report->employability_score }}/100
</div>

<h2>Career Archetype Map</h2>

<table>
    <tr>
        <th>Archetype</th>
        <th>Score</th>
    </tr>

    @foreach($report->archetype_scores as $name => $value)
        <tr>
            <td>{{ $name }}</td>
            <td>{{ $value }}/100</td>
        </tr>
    @endforeach
</table>

<h2>Career Intelligence</h2>

<p>{{ $report->narrative }}</p>

<h2>Recommendations</h2>

<ul>
    @foreach($report->recommendations as $recommendation)
        <li>{{ $recommendation }}</li>
    @endforeach
</ul>

</body>
</html>