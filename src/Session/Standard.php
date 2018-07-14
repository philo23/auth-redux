<?php
namespace WBC\Auth\Session;

use WBC\Auth\UserContainer;

class Standard implements SessionInterface
{
    /** @var string */
    private $namespace;

    /**
     * @param string $namespace
     */
    public function __construct($namespace = 'auth')
    {
        $this->namespace = $namespace;

        session_start();

        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }
    }

    public function setUser(UserContainer $user)
    {
        $_SESSION[$this->namespace]['username'] = $user->getUsername();
        $_SESSION[$this->namespace]['logged_in_at'] = new \DateTime();
    }

    public function getUsername()
    {
        if (!isset($_SESSION[$this->namespace]['username'])) {
            return null;
        }
        return $_SESSION[$this->namespace]['username'];
    }

    public function getUserLoggedInAt()
    {
        if (!isset($_SESSION[$this->namespace]['logged_in_at'])) {
            return null;
        }
        return $_SESSION[$this->namespace]['logged_in_at'];
    }

    public function clearUser()
    {
        unset(
            $_SESSION[$this->namespace]['username'],
            $_SESSION[$this->namespace]['logged_in_at']
        );
    }
}
