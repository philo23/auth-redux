<?php
namespace WBC\Auth\Storage;

interface StorageInterface
{
    /**
     * @param string $username
     * @return null|\WBC\Auth\UserContainer
     */
    public function getUser($username);

    /**
     * @param string $username
     * @param string $hash
     * @return void
     */
    public function updatePassword($username, $hash);
}
