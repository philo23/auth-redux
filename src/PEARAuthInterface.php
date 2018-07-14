<?php
namespace WBC\Auth;

interface PEARAuthInterface
{
    public function addUser($username, $password, $additional = '');
    public function attachLogObserver($observer);
    public function changePassword($username, $password);
    public function checkAuth();
    public function getAuth();
    public function getAuthData($name = null);
    public function getPostPasswordField();
    public function getPostUsernameField();
    public function getStatus();
    public function getUsername();
    public function listUsers();
    public function log($message, $level);
    public function logout();
    public function removeUser($username);
    public function setAdvancedSecurity($flag = true);
    public function setAllowLogin($allowLogin = true);
    public function setAuth($username);
    public function setAuthData($name, $value, $overwrite = true);
    public function deleteAuthData($name); // UNDOCUMENTED
    public function sessionValidThru();
    public function setCheckAuthCallback(callable $callback);
    public function setExpire($time, $add = false);
    public function setFailedLoginCallback(callable $callback);
    public function setIdle($time, $add = false);
    public function setLoginCallback(callable $callback);
    public function setLogoutCallback(callable $callback);
    public function setSessionName($name = 'session');
    public function setShowLogin($showLogin = true);
    public function start();
    public static function staticCheckAuth($options = null);
}
