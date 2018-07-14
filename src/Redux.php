<?php
namespace WBC\Auth;

use WBC\Auth\Storage\StorageInterface;
use WBC\Auth\Hashing\HashingInterface;
use WBC\Auth\Session\SessionInterface;
use WBC\Auth\Exceptions\IncorrectPassword;
use WBC\Auth\Exceptions\UnknownUsername;
use WBC\Auth\Exceptions\NotLoggedIn;

class Redux
{
    /** @var StorageInterface */
    private $storage;
    /** @var HashingInterface */
    private $hashing;
    /** @var SessionInterface */
    private $session;

    /** @var null|UserContainer */
    private $user = null;

    /**
     * @param StorageInterface $storage
     * @param HashingInterface $hashing
     * @param SessionInterface $session
     */
    public function __construct(StorageInterface $storage, HashingInterface $hashing, SessionInterface $session)
    {
        $this->storage = $storage;
        $this->hashing = $hashing;
        $this->session = $session;
    }

    /**
     * @return void
     */
    public function start()
    {
        $username = $this->session->getUsername();
        if ($username) {
            $this->user = $this->storage->getUser($username);
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return void
     * @throws IncorrectPassword
     * @throws UnknownUsername
     */
    public function login($username, $password)
    {
        $user = $this->storage->getUser($username);
        if ($user === null) {
            throw new UnknownUsername();
        }

        if (!$this->hashing->verify($password, $user->getPasswordHash())) {
            throw new IncorrectPassword();
        }

        if ($this->hashing->needsRehash($user->getPasswordHash())) {
            $new_hash = $this->hashing->hash($password);
            $this->storage->updatePassword($username, $new_hash);
        }

        $this->session->setUser($user);

        $this->user = $user;
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->session->clearUser();

        $this->user = null;
    }

    /**
     * @param string $new_password
     * @return void
     * @throws NotLoggedIn
     */
    public function changePassword($new_password)
    {
        if (!$this->isLoggedIn()) {
            throw new NotLoggedIn();
        }

        $hash = $this->hashing->hash($new_password);

        $this->storage->updatePassword(
            $this->user->getUsername(),
            $hash
        );

        $this->user->setPasswordHash($hash);
    }

    /**
     * @param string $password
     * @return string
     */
    public function hash($password)
    {
        return $this->hashing->hash($password);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->user !== null;
    }

    /**
     * @return null|UserContainer
     */
    public function getUser()
    {
        return $this->user;
    }
}
