<?php
namespace WBC\Auth\Hashing;

interface HashingInterface
{
    /**
     * @param string $password
     * @return string
     */
    public function hash($password);

    /**
     * @param string $hash
     * @return bool
     */
    public function needsRehash($hash);

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify($password, $hash);
}
