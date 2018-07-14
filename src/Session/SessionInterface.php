<?php
namespace WBC\Auth\Session;

use WBC\Auth\UserContainer;

interface SessionInterface
{
    /**
     * @param UserContainer $user
     * @return void
     */
    public function setUser(UserContainer $user);

    /**
     * @return null|string
     */
    public function getUsername();

    /**
     * @return null|\DateTime
     */
    public function getUserLoggedInAt();

    /**
     * @return void
     */
    public function clearUser();
}
