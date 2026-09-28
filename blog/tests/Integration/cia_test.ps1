$baseUrl = "http://127.0.0.1:8000"

$endpoints = @(
    @{ Name = "CIA Career"; Path = "/cia-career" },
    @{ Name = "Internship Matching"; Path = "/internship-matches" },
    @{ Name = "Login"; Path = "/login" },
    @{ Name = "Dashboard"; Path = "/dashboard" },
    @{ Name = "Profile"; Path = "/profile" },
    @{ Name = "Career Intelligence"; Path = "/career-intelligence/1" }
)

Write-Host ""
Write-Host "CIA CAREER INTEGRATION TEST"
Write-Host "============================"

$passed = 0
$total = $endpoints.Count

foreach ($endpoint in $endpoints) {

    try {
        $response = Invoke-WebRequest `
            -Uri ($baseUrl + $endpoint.Path) `
            -Method GET `
            -MaximumRedirection 0 `
            -ErrorAction Stop

        $status = $response.StatusCode

        if ($status -ne 500) {
            Write-Host "PASS  $($endpoint.Name) - HTTP $status"
            $passed++
        }
        else {
            Write-Host "FAIL  $($endpoint.Name) - HTTP 500"
        }
    }
    catch {
        $status = $_.Exception.Response.StatusCode.value__

        if ($status -and $status -ne 500) {
            Write-Host "PASS  $($endpoint.Name) - HTTP $status"
            $passed++
        }
        else {
            Write-Host "FAIL  $($endpoint.Name)"
        }
    }
}

Write-Host "============================"
Write-Host "RESULT: $passed/$total endpoints passed"
Write-Host "============================"