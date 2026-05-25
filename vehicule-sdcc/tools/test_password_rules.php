<?php
$tests = [
    'Short1' => 'Ab1',
    'NoUpper' => 'password1',
    'NoLower' => 'PASSWORD1',
    'NoDigit' => 'Password',
    'Valid8' => 'Passw0rd',
    'ValidWithSpecial' => 'Passw0rd!',
    'ValidLong' => 'StrongPass123',
];

function check_rule($pw) {
    $min = strlen($pw) >= 8;
    $hasLower = preg_match('/[a-z]/', $pw);
    $hasUpper = preg_match('/[A-Z]/', $pw);
    $hasDigit = preg_match('/[0-9]/', $pw);
    return [
        'min' => $min,
        'lower' => (bool)$hasLower,
        'upper' => (bool)$hasUpper,
        'digit' => (bool)$hasDigit,
        'ok' => ($min && $hasLower && $hasUpper && $hasDigit),
    ];
}

foreach ($tests as $name => $pw) {
    $res = check_rule($pw);
    echo "$name: $pw -> " . ($res['ok'] ? 'PASS' : 'FAIL') . "\n";
    echo "  details: min={$res['min']}, lower={$res['lower']}, upper={$res['upper']}, digit={$res['digit']}\n";
}
