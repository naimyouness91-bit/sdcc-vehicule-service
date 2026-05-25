$resultFile = 'scripts/auth_test_result.txt'
Remove-Item $resultFile -ErrorAction SilentlyContinue
Function Log($s){ Add-Content $resultFile ("$(Get-Date -Format o) `t $s") }

# Start session
$s = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# 1) GET login
Log "GET /login"
$r = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -WebSession $s -UseBasicParsing
if (-not $r) { Log "ERROR: no response from /login"; exit 1 }
$m = [regex]::Match($r.Content,'name="_token" value="([^"]+)"')
if (-not $m.Success) { Log "ERROR: CSRF token not found on /login"; exit 1 }
$token = $m.Groups[1].Value
Log "CSRF token obtained"

# 2) POST login (use sdcc domain admin)
Log "POST /login (authenticate admin@sdcc.ma)"
$body = @{email='admin@sdcc.ma'; password='password'; _token=$token}
try {
    $resp = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -Method POST -Body $body -WebSession $s -UseBasicParsing -MaximumRedirection 10 -ErrorAction Stop
    if ($resp.StatusCode -eq 200 -or $resp.StatusCode -in 300..399) { Log "Login response status: $($resp.StatusCode)" } else { Log "Login unexpected status: $($resp.StatusCode)" }
} catch {
    Log "Login POST resulted in an exception: $($_.Exception.Message)"
    # Try to capture response body if available
    try {
        $errResp = $_.Exception.Response
        if ($errResp) {
            $reader = New-Object System.IO.StreamReader($errResp.GetResponseStream())
            $content = $reader.ReadToEnd()
            $content | Out-File scripts/login_error.html -Encoding utf8
            Log "Saved login error response to scripts/login_error.html"
        }
    } catch {
        Log "No response body available for login error"
    }
    exit 1
}

# 3) GET /admin
Log "GET /admin"
$r2 = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/admin' -WebSession $s -UseBasicParsing -MaximumRedirection 10 -ErrorAction SilentlyContinue
$status = $r2.StatusCode
Log "/admin status: $status"
if ($r2.Content -match '403|Unauthorized') { Log "WARNING: access denied /admin" }

# 4) GET create reservation page
Log "GET /admin/reservations/create"
$r3 = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/admin/reservations/create' -WebSession $s -UseBasicParsing -ErrorAction SilentlyContinue
Log "/admin/reservations/create status: $($r3.StatusCode)"
$m = [regex]::Match($r3.Content,'name="_token" value="([^"]+)"')
$tokenForm = $null
if ($m.Success) { $tokenForm = $m.Groups[1].Value; Log "Form CSRF token found" } else { Log "No form CSRF token found on create page" }

# 5) Get available car id
Log "Query DB for available car"
$carId = & php scripts/get_available_car.php
if (-not $carId) { Log "No available car with status 'disponible' found. Aborting create."; exit 1 }
Log "Available car id: $carId"

# 6) Create reservation POST
$today = Get-Date -Format yyyy-MM-dd
$tomorrow = (Get-Date).AddDays(1).ToString('yyyy-MM-dd')
$createBody = @{
    user_id = '1'
    car_id = $carId
    destination = 'Test - Integration'
    start_date = $today
    start_time = '09:00'
    end_date = $tomorrow
    end_time = '17:00'
    kilometers = '10'
    reason = 'Test reservation creation'
    _token = $tokenForm
}
Log "POST /admin/reservations (create)"
$createResp = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/admin/reservations' -Method POST -Body $createBody -WebSession $s -UseBasicParsing -MaximumRedirection 10 -ErrorAction SilentlyContinue
Log "Create response status: $($createResp.StatusCode)"
# Save create response body for inspection
if ($createResp -and $createResp.Content) {
    $createResp.Content | Out-File scripts/create_resp.html -Encoding utf8
    Log "Saved create response body to scripts/create_resp.html"
}

# 7) Get last demande id from DB
$demandeId = & php scripts/get_last_demande.php
if (-not $demandeId) { Log "ERROR: No demande found after create"; exit 1 }
Log "Created demande id: $demandeId"

# 8) GET edit page
Log "GET /admin/reservations/$demandeId/edit"
$editPage = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/reservations/$demandeId/edit" -WebSession $s -UseBasicParsing -ErrorAction SilentlyContinue
Log "Edit page status: $($editPage.StatusCode)"
$m = [regex]::Match($editPage.Content,'name="_token" value="([^"]+)"')
$editToken = $null
if ($m.Success) { $editToken = $m.Groups[1].Value; Log "Edit form CSRF token found" } else { Log "No CSRF token on edit page" }

# 9) Update reservation (PUT)
$updateBody = @{
    user_id = '1'
    car_id = $carId
    destination = 'Test - Integration Edited'
    start_date = $today
    start_time = '10:00'
    end_date = $tomorrow
    end_time = '16:30'
    kilometers = '12'
    reason = 'Edited by automated test'
    status = 'pending'
    _token = $editToken
    _method = 'PUT'
}
Log "POST (as PUT) /admin/reservations/$demandeId"
$updateResp = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/reservations/$demandeId" -Method Post -Body $updateBody -WebSession $s -UseBasicParsing -MaximumRedirection 10 -ErrorAction SilentlyContinue
Log "Update response status: $($updateResp.StatusCode)"

# 10) DELETE reservation
Log "POST (as DELETE) /admin/reservations/$demandeId"
# Need CSRF token - reuse editToken and spoof method
$delBody = @{ _token = $editToken; _method = 'DELETE' }
$delResp = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/reservations/$demandeId" -Method Post -Body $delBody -WebSession $s -UseBasicParsing -ErrorAction SilentlyContinue
# For DELETE, some controllers return JSON
if ($delResp -and $delResp.StatusCode) { Log "Delete response status: $($delResp.StatusCode)" } else { Log "Delete: no response" }

# 11) Check latest logs for errors
$logPath = 'storage/logs/laravel.log'
if (Test-Path $logPath) {
    $last = Get-Content $logPath -Tail 200
    $last | Out-File scripts/laravel_tail.txt -Encoding utf8
    $errors = Select-String -Path scripts/laravel_tail.txt -Pattern 'ERROR|CRITICAL|Exception' -SimpleMatch
    if ($errors) {
        Log "Found errors in laravel.log:"
        $errors | ForEach-Object { Log $_.ToString() }
    } else {
        Log "No new ERROR/CRITICAL/Exception entries found in laravel.log tail"
    }
} else {
    Log "laravel.log not found"
}

Log "AUTH TESTS COMPLETED"
Write-Output "Done. Results at $resultFile"
