<?php
namespace WBC\Auth\Session;

use WBC\Auth\UserContainer;

class PEARAuthBackwardsCompatible extends Standard
{

    public function setUser(UserContainer $user)
    {
        parent::setUser($user);

        $_SESSION[$this->namespace]['registered'] = true;
        $_SESSION[$this->namespace]['data'] = $user->data;
        $_SESSION[$this->namespace]['timestamp'] = time();
        $_SESSION[$this->namespace]['session'] = time();
    }

    public function clearUser()
    {
        parent::clearUser();

        unset(
            $_SESSION[$this->namespace]['registered'],
            $_SESSION[$this->namespace]['data'],
            $_SESSION[$this->namespace]['timestamp'],
            $_SESSION[$this->namespace]['session']
        );
    }

}
