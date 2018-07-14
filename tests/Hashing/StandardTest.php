<?php
namespace WBC\Tests\Auth\Hashing;

use WBC\Auth\Hashing\Standard;

class StandardTest extends \PHPUnit\Framework\TestCase
{
    /** @var Standard */
    private $hash;

    public function setUp()
    {
        $this->hash = new Standard();
    }

    public function passwordProvider()
    {
        return [
            [
                'NLpagH8DXWpp',
                '$2y$10$exhK0GUD4yMbVpX/xLkSSeXLSyvRB1.uiITIAVVX/maz/b04OWkoS'
            ],
            [
                'TUVvQgGMRtLg',
                '$2y$10$5Iwl0JveagKIzAMkVfMS/.CzDSej5gAXYieLej3flKyLSenzEly8C'
            ],
            [
                'CRjhG3vYK6MM',
                '$2y$10$iaKV6Iem9JQOAUkD1owFmeGXfq3KZSOfwwWJ.aNie2K2abmgnSHeK'
            ],
            [
                'zADE6ceLfpcE',
                '$2y$10$1tmY8HWNdZweySZvfPFWw.SBFf.ozn8BlQyg7GqAMNYcYYzMmb5MK'
            ],
            [
                'ExxB5mWpVNk5',
                '$2y$10$/TImJPIeTrmK0VX5chBNKuNkQm8IPEb/Qy7BBmYvLH/PsyW/aCFmC'
            ]
        ];
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testVerify($password, $hash)
    {
        $this->assertTrue($this->hash->verify($password, $hash));
    }
}