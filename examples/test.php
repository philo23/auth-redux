<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$db = require 'shared.php';

$auth = new WBC\Auth\Redux(
    new WBC\Auth\Storage\PDO($db),
    new WBC\Auth\Hashing\Standard(),
    new WBC\Auth\Session\Standard()
);

$auth->start();

/**
 * You can choose to either catch LoginError which covers both
 * unknown usernames and incorrect passwords, or if you want
 * to know which you can catch UnknownUsername or IncorrectPassword
 */

try {

    list(, $username, $password) = $argv;
    $auth->login($username, $password);

    echo $auth->isLoggedIn() ? 'Logged in' : 'Login failed', PHP_EOL;
    print_r($auth->getUser());

} catch (WBC\Auth\Exceptions\LoginError $e) {
    echo 'Invalid username or password.', PHP_EOL;
}
/*
} catch (WBC\Auth\Exceptions\UnknownUsername $e) {
    echo 'Unknown username.', PHP_EOL;
} catch (WBC\Auth\Exceptions\IncorrectPassword $e) {
    echo 'Password incorrect.', PHP_EOL;
}
*/
