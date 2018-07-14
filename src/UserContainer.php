<?php
namespace WBC\Auth;

class UserContainer
{
    /** @var string */
    private $username;
    /** @var string */
    private $password_hash;
    /** @var array */
    public $data = [];

    /**
     * @param string $username
     * @param string $password_hash
     */
    public function __construct($username, $password_hash)
    {
        $this->username = $username;
        $this->password_hash = $password_hash;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * @param string $password_hash
     * @return void
     */
    public function setPasswordHash($password_hash)
    {
        $this->password_hash = $password_hash;
    }
}
