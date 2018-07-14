<?php
namespace WBC\Tests\Auth\Hashing;

use WBC\Auth\Hashing\LegacyHash;

class LegacyHashTest extends \PHPUnit\Framework\TestCase
{
    /** @var LegacyHash */
    private $hash_md5, $hash_sha1, $hash_sha256;

    public function setUp()
    {
        $this->hash_md5 = new LegacyHash(LegacyHash::ALGO_MD5);
        $this->hash_sha1 = new LegacyHash(LegacyHash::ALGO_SHA1);
        $this->hash_sha256 = new LegacyHash(LegacyHash::ALGO_SHA256);
    }

    public function passwordProvider()
    {
        return [
            [
                'NLpagH8DXWpp',
                'b51f2ec6aebed841a8057cd56614a904',
                '40a80a709ac3d42b7ffa230649e7e755a5711de4',
                '6597aca2fe9f59939f69001a1295530a9232fbc1dc1587941b9d003d5bf69a24'
            ],
            [
                'TUVvQgGMRtLg',
                'f1857afbd8fe6e4dbc9845dcf34b5f23',
                'a3922ac20715cb7e23a578302f6a7631ea0fafe8',
                '00e38101207d8638c4e1b0b7cbca31aaa43fa8017aac6604c35920884ac5302a'
            ],
            [
                'CRjhG3vYK6MM',
                '131cd2c1be043108b8b099e1dacac01f',
                '9421f3ad5cf8d1bebdfa2bb41b643be721702c34',
                '73812b213e06aeda60537731da1f22bf8959d66982ea63a7f84a1b17e0f17219'
            ],
            [
                'zADE6ceLfpcE',
                '7de6735001d004d828e8d553ec20dbfd',
                '8d69ee91da77c784e0ff0ce5886d3679afc21acf',
                'e60bf9b37b641f3623ead00a39ca9d917634765bb63b3b1a163550da1091914c'
            ],
            [
                'ExxB5mWpVNk5',
                '44bc5b87c862ef206e0ba5334d2ec9b0',
                '7312dc9f566d8cc25b6490c5cb833dd0bd261d80',
                '78fa1ee70e9d517e20760d61fa45e96c8eebce152fe29a43d5f4fd054ccbab5d'
            ]
        ];
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testHashing($password, $md5, $sha1, $sha256)
    {
        $this->assertEquals($md5, $this->hash_md5->hash($password));
        $this->assertEquals($sha1, $this->hash_sha1->hash($password));
        $this->assertEquals($sha256, $this->hash_sha256->hash($password));
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testNeedsRehash($password, $md5, $sha1, $sha256)
    {
        $this->assertFalse($this->hash_md5->needsRehash($md5));
        $this->assertFalse($this->hash_sha1->needsRehash($sha1));
        $this->assertFalse($this->hash_sha256->needsRehash($sha256));
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testVerify($password, $md5, $sha1, $sha256)
    {
        $this->assertTrue($this->hash_md5->verify($password, $md5));
        $this->assertTrue($this->hash_sha1->verify($password, $sha1));
        $this->assertTrue($this->hash_sha256->verify($password, $sha256));
    }
}