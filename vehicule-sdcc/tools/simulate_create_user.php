<?php
// Simulate login and user creation with CSRF and cookie handling
$base = 'http://127.0.0.1:8000';
$cookie = __DIR__ . '/cookies.txt';
@unlink($cookie);

function curl_get($url, $cookie) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SimClient/1.0');
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['body'=>$res, 'info'=>$info];
}

function curl_post($url, $data, $cookie, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SimClient/1.0');
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err = curl_error($ch);
    curl_close($ch);
    return ['body'=>$res, 'info'=>$info, 'error'=>$err];
}

echo "Fetching login page...\n";
$r = curl_get($base . '/login', $cookie);
if ($r['info']['http_code'] !== 200) {
    echo "GET /login returned " . $r['info']['http_code'] . "\n";
}
$body = $r['body'];
if (!preg_match('/name="_token" value="([^"]+)"/', $body, $m)) {
    echo "Failed to find CSRF token on login page.\n";
    exit(1);
}
$token = $m[1];
echo "CSRF token: $token\n";

// Login as superadmin
$loginData = [
    '_token' => $token,
    'email' => 'superadmin@sdcc.ma',
    'password' => 'ChangeMe@123456',
];
echo "Posting login...\n";
$resp = curl_post($base . '/login', $loginData, $cookie, ['Expect:']);
echo "Login HTTP: " . $resp['info']['http_code'] . " final_url=" . $resp['info']['url'] . "\n";

// Access create user form
echo "Fetching create user form...\n";
$r2 = curl_get($base . '/utilisateurs/create', $cookie);
echo "GET /utilisateurs/create HTTP: " . $r2['info']['http_code'] . "\n";
$body2 = $r2['body'];
if (!preg_match('/name="_token" value="([^"]+)"/', $body2, $m2)) {
    echo "Failed to find CSRF token on create form.\n";
    // Output a snippet
    echo substr($body2,0,800);
    exit(1);
}
$token2 = $m2[1];
echo "CSRF for create form: $token2\n";

// Prepare valid user data
$newUser = [
    '_token' => $token2,
    'name' => 'Test User',
    'email' => 'test.user+' . time() . '@sdcc.ma',
    'password' => 'Passw0rd',
    'password_confirmation' => 'Passw0rd',
    // choose a service value from dropdown
    'service' => 'Moyens Généraux',
    'role' => 'admin',
];

echo "Posting create user (valid password)...\n";
$resp2 = curl_post($base . '/utilisateurs', $newUser, $cookie, ['Expect:','Referer: ' . $base . '/utilisateurs/create']);
echo "Create HTTP: " . $resp2['info']['http_code'] . " final_url=" . $resp2['info']['url'] . "\n";
// Check for success message in body
if (strpos($resp2['body'], 'Utilisateur créé') !== false || strpos($resp2['body'], 'créé avec succès') !== false) {
    echo "User creation appears successful (success message found).\n";
} else {
    echo "No success message detected. Output snippet:\n";
    echo substr($resp2['body'],0,1400);
}

// Now test invalid password (too short)
if (!preg_match('/name="_token" value="([^"]+)"/', $resp2['body'], $m3)) {
    // refetch create form to get fresh token
    $r3 = curl_get($base . '/utilisateurs/create', $cookie);
    preg_match('/name="_token" value="([^"]+)"/', $r3['body'], $m3);
}
$token3 = $m3[1] ?? $token2;
$invalidUser = [
    '_token' => $token3,
    'name' => 'Bad Password',
    'email' => 'badpw.' . time() . '@sdcc.ma',
    'password' => 'short',
    'password_confirmation' => 'short',
    'service' => 'Moyens Généraux',
    'role' => 'admin',
];

echo "Posting create user (invalid password)...\n";
$resp3 = curl_post($base . '/utilisateurs', $invalidUser, $cookie, ['Expect:','Referer: ' . $base . '/utilisateurs/create']);
echo "Invalid create HTTP: " . $resp3['info']['http_code'] . " final_url=" . $resp3['info']['url'] . "\n";

// Expect validation errors in response body
if (strpos($resp3['body'], 'Le mot de passe doit contenir') !== false || strpos($resp3['body'], 'Le mot de passe doit contenir au moins') !== false) {
    echo "Validation error detected as expected.\n";
    // Extract error list snippet
    if (preg_match('/<ul[^>]*>(.*?)<\/ul>/s', $resp3['body'], $ul)) {
        echo "Errors:\n" . strip_tags($ul[1]) . "\n";
    }
} else {
    echo "No validation error message detected. Snippet:\n";
    echo substr($resp3['body'],0,1000);
}

// Done
echo "Simulation completed.\n";

?>