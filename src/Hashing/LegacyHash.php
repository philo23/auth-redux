<?php
namespace WBC\Auth\Hashing;

// USING THIS HASHING CLASS IS A TERRIBLE IDEA
// IT'S PROVIDED FOR BACKWARDS COMPATIBILITY ONLY
// PLEASE DON'T USE IT!

class LegacyHash implements HashingInterface
{
    const ALGO_MD5 = 'md5';
    const ALGO_SHA1 = 'sha1';
    const ALGO_SHA256 = 'sha256';

    /** @var string */
    private $algorithm;

    public function __construct($algorithm)
    {
        if (!in_array($algorithm, hash_algos())) {
            throw new \Exception(
                'Unknown hashing algorithm: ' . $algorithm
            );
        }

        $this->algorithm = $algorithm;
    }

    public function hash($password)
    {
        return hash($this->algorithm, $password);
    }

    public function needsRehash($hash)
    {
        return false;
    }

    public function verify($password, $hash)
    {
        return hash_equals($hash, $this->hash($password));
    }
}
