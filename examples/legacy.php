<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$db = require 'shared.php';

WBC\Auth\LegacyPEARAuth::defineLegacyConstants();

list(, $username, $password) = $argv;
$_POST['username'] = $username;
$_POST['password'] = $password;

$auth = new WBC\Auth\Redux(
    new WBC\Auth\Storage\PDO($db),
    new WBC\Auth\Hashing\Standard(),
    new WBC\Auth\Session\Standard()
);

function loginForm ($username, $reason) {
    echo 'Show login form', PHP_EOL;
    echo $username, PHP_EOL, $reason, PHP_EOL;
    exit;
}

$legacy = new WBC\Auth\LegacyPEARAuth($auth, 'loginForm');
$legacy->start();

echo 'Status: ', $legacy->getStatus(), PHP_EOL;

if ($legacy->checkAuth()) {
    echo 'Logged in', PHP_EOL;
}

echo 'Username: ', print_r($legacy->getUsername(), true), PHP_EOL;
echo 'Auth data: ', print_r($legacy->getAuthData('extra_data'), true), PHP_EOL;
echo 'Auth: ', print_r($legacy->getAuth(), true), PHP_EOL;
