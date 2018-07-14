<?php
namespace WBC\Tests\Auth\Hashing;

use WBC\Auth\Hashing\LegacyPlainText;

class LegacyPlainTextTest extends \PHPUnit\Framework\TestCase
{
    /** @var LegacyPlainText */
    private $hash;

    public function setUp()
    {
        $this->hash = new LegacyPlainText();
    }

    public function passwordProvider()
    {
        return [
            [
                'NLpagH8DXWpp'
            ],
            [
                'TUVvQgGMRtLg'
            ],
            [
                'CRjhG3vYK6MM'
            ],
            [
                'zADE6ceLfpcE'
            ],
            [
                'ExxB5mWpVNk5'
            ]
        ];
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testHashing($password)
    {
        $this->assertEquals($password, $this->hash->hash($password));
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testNeedsRehash($password)
    {
        $this->assertFalse($this->hash->needsRehash($password));
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testVerify($password)
    {
        $this->assertTrue($this->hash->verify($password, $password));
    }
}