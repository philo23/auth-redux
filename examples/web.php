<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$db = require 'shared.php';

$auth = new WBC\Auth\Redux(
    new WBC\Auth\Storage\PDO($db),
    new WBC\Auth\Hashing\Standard(),
    new WBC\Auth\Session\Standard()
);

$auth->start();

$action = (string)filter_input(INPUT_GET, 'action');
switch ($action) {
    case 'login':
        $username = (string)filter_input(INPUT_POST, 'username');
        $password = (string)filter_input(INPUT_POST, 'password');

        try {
            $auth->login($username, $password);

            $_SESSION['message'] = 'login';
            header('Location: web.php');
        //} catch (WBC\Auth\Exceptions\LoginError $e) {
            // You can either catch this one exception
            //$_SESSION['message'] = 'error-unknown';
            //header('Location: web.php');
        } catch (WBC\Auth\Exceptions\UnknownUsername $e) {
            // Other both of these individually
            $_SESSION['message'] = 'error-username';
            header('Location: web.php');
        } catch (WBC\Auth\Exceptions\IncorrectPassword $e) {
            // Depending on how much detail you want to expose
            $_SESSION['message'] = 'error-password';
            header('Location: web.php');
        }
        exit;

    case 'logout':
        if ($auth->isLoggedIn()) {
            $auth->logout();
            $_SESSION['message'] = 'logout';
        }
        header('Location: web.php');
        exit;
}

$logged_in = $auth->isLoggedIn();
$user = $auth->getUser();

$message_id = $_SESSION['message'] ?? null;
$message = null;
unset($_SESSION['message']);

switch ($message_id) {
    case 'error-unknown':
        $message = 'Login failed, bad username/password combination.';
        break;

    case 'error-username':
        $message = 'Login failed, uknown username.';
        break;

    case 'error-password':
        $message = 'Login failed, incorrect password.';
        break;

    case 'login':
        $message = 'Successfully logged in.';
        break;

    case 'logout':
        $message = 'Successfully logged out.';
        break;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Auth Test</title>
    <style>
    html, body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 14px;
    }
    .message {
        border: 1px solid #CCC;
        background-color: #F0F0F0;
        padding: 10px;
    }
    form {
        margin-bottom: 20px;
    }
    fieldset {
        border: 1px solid #CCC;
        background-color: #F0F0F0;
        margin-bottom: 20px;
        padding: 10px;
    }
    fieldset legend {
        border: 1px solid #CCC;
        background-color: #FFF;
        padding: 2px 5px 0;
    }
    fieldset pre {
        padding: 0;
        margin: 0;
        overflow: auto;
    }
    </style>
</head>
<body>
    <?php if ($message) { ?>
    <p class="message">
        <?php echo htmlspecialchars($message) ?>
    </p>
    <?php } ?>

    <?php if ($logged_in) { ?>
    <p>
        Welcome <?php echo htmlspecialchars($user->getUsername()) ?>
        <a href="web.php?action=logout">logout</a>
    </p>
    <?php } else { ?>
    <form action="web.php?action=login" method="post">
        <label>
            Username
            <input type="text" name="username">
        </label>
        <label>
            Password
            <input type="password" name="password">
        </label>
        <button type="submit">
            Login
        </button>
    </form>
    <?php } ?>


    <fieldset>
        <legend>Session Data</legend>
        <pre><?php echo htmlspecialchars(print_r($_SESSION ?? [], true)) ?></pre>
    </fieldset>

    <fieldset>
        <legend>Cookie Data</legend>
        <pre><?php echo htmlspecialchars(print_r($_COOKIE ?? [], true)) ?></pre>
    </fieldset>
</body>
</html>
