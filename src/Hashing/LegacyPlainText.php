<?php
namespace WBC\Auth\Hashing;

// USING THIS HASHING CLASS IS A REALLY TERRIBLE IDEA
// IT'S PROVIDED FOR BACKWARDS COMPATIBILITY ONLY
// PLEASE DON'T USE IT!

class LegacyPlainText implements HashingInterface
{
    public function hash($password)
    {
        return $password;
    }

    public function needsRehash($hash)
    {
        return false;
    }

    public function verify($password, $hash)
    {
        return hash_equals($hash, $password);
    }
}
