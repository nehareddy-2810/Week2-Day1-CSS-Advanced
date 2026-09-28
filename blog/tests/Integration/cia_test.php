<?php

$baseUrl = 'http://127.0.0.1:8000';

$endpoints = [
    'CIA Career' => '/cia-career',
    'Internship Matching' => '/internship-matches',
    'Login' => '/login',
    'Dashboard' => '/dashboard',
    'Profile' => '/profile',
    'Career Intelligence' => '/career-intelligence/1',
];

echo PHP_EOL;
echo "CIA CAREER INTEGRATION TEST" . PHP_EOL;
echo "============================" . PHP_EOL;

$passed = 0;
$total = count($endpoints);

foreach ($endpoints as $name => $endpoint) {

    $ch = curl_init($baseUrl . $endpoint);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    $response = curl_exec($ch);

    if ($response === false) {
        echo "FAIL  $name - Server unavailable" . PHP_EOL;
    } else {
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status != 500) {
            echo "PASS  $name - HTTP $status" . PHP_EOL;
            $passed++;
        } else {
            echo "FAIL  $name - HTTP 500" . PHP_EOL;
        }
    }

    curl_close($ch);
}

echo "============================" . PHP_EOL;
echo "RESULT: $passed/$total endpoints passed" . PHP_EOL;
echo "============================" . PHP_EOL;