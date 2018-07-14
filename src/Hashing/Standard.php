<?php
namespace WBC\Auth\Hashing;

class Standard implements HashingInterface
{
    public function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
