<?php
namespace WBC\Tests\Auth\Storage;

use WBC\Auth\Storage\File;
use WBC\Auth\UserContainer;

class FileTest extends \PHPUnit\Framework\TestCase
{
    /** @var File */
    private $storage;

    public function setUp()
    {
        $fh = fopen('php://memory', 'rw');

        foreach ($this->userProvider() as $user) {
            list($username, $password) = $user;
            fwrite($fh, "{$username}\t{$password}\r\n");
        }

        $this->storage = new File($fh);
    }

    public function userProvider()
    {
        return [
            ['philip', 'NLpagH8DXWpp'],
            ['alice', 'CRjhG3vYK6MM'],
            ['bob', 'TUVvQgGMRtLg']
        ];
    }

    public function invalidProvider()
    {
        return [
            ['ailsa'],
            ['brad'],
            ['christiana']
        ];
    }

    /**
     * @dataProvider userProvider
     */
    public function testValidLookup($username, $password)
    {
        $this->assertNotEmpty($username);
        $this->assertNotEmpty($password);

        $result = $this->storage->getUser($username);
        $this->assertInstanceOf(UserContainer::CLASS, $result);
        $this->assertEquals($username, $result->getUsername());
        $this->assertEquals($password, $result->getPasswordHash());
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalidLookup($username)
    {
        $result = $this->storage->getUser($username);
        $this->assertNull($result);
    }
}