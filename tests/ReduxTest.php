<?php
namespace WBC\Tests\Auth;

use WBC\Auth\Redux;

class ReduxTest extends \PHPUnit\Framework\TestCase
{
    private function mockStorage()
    {
        return $this->createMock(\WBC\Auth\Storage\StorageInterface::class);
    }

    private function mockHash()
    {
        return $this->createmock(\WBC\Auth\Hashing\HashingInterface::class);
    }

    private function mockSession()
    {
        return $this->createMock(\WBC\Auth\Session\SessionInterface::class);
    }

    public function testStartWithNoLogin()
    {
        $mock_storage = $this->mockStorage();
        $mock_storage->method('getUser')
            ->willReturn(null);

        $mock_hash = $this->mockHash();

        $mock_session = $this->mockSession();
        $mock_session->method('getUsername')
            ->willReturn(null);

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);
        $redux->start();

        $this->assertFalse($redux->isLoggedIn());
        $this->assertNull($redux->getUser());
    }

    public function testLoginWithUnknownUsername()
    {
        $user = new \WBC\Auth\UserContainer('username', 'password_hash');

        $mock_storage = $this->mockStorage();
        $mock_storage->method('getUser')
            ->willReturn(null);

        $mock_hash = $this->mockHash();

        $mock_session = $this->mockSession();
        $mock_session->method('getUsername')
            ->willReturn($user->getUsername());

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);

        $this->expectException(\WBC\Auth\Exceptions\UnknownUsername::class);
        $redux->start();

        $redux->login('incorrect', 'incorrect');

        $this->assertFalse($redux->isLoggedIn());
        $this->assertNull($redux->getUser());
    }

    public function testLoginWithIncorrectPassword()
    {
        $user = new \WBC\Auth\UserContainer('username', 'password_hash');

        $mock_storage = $this->mockStorage();
        $mock_storage->method('getUser')
            ->willReturn($user);

        $mock_hash = $this->mockHash();

        $mock_session = $this->mockSession();
        $mock_session->method('getUsername')
            ->willReturn($user->getUsername());

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);

        $this->expectException(\WBC\Auth\Exceptions\IncorrectPassword::class);
        $redux->start();

        $redux->login($user->getUsername(), 'incorrect');

        $this->assertFalse($redux->isLoggedIn());
        $this->assertNull($redux->getUser());
    }

    public function testLoginWithCorrect()
    {
        $user = new \WBC\Auth\UserContainer('username', 'password_hash');

        $mock_storage = $this->mockStorage();
        $mock_storage->method('getUser')
            ->willReturn($user);

        $mock_hash = $this->mockHash();
        $mock_hash->method('verify')
            ->willReturn(true);

        $mock_session = $this->mockSession();
        $mock_session->method('getUsername')
            ->willReturn($user->getUsername());

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);
        $redux->start();

        $redux->login('username', 'password');

        $this->assertTrue($redux->isLoggedIn());
        $this->assertNotNull($redux->getUser());
        $this->assertEquals($redux->getUser()->getUsername(), $user->getUsername());
    }

    public function testChangePasswordLoggedOut()
    {
        $mock_storage = $this->mockStorage();

        $mock_hash = $this->mockHash();

        $mock_session = $this->mockSession();
        
        $redux = new Redux($mock_storage, $mock_hash, $mock_session);
        $redux->start();

        $this->expectException(\WBC\Auth\Exceptions\NotLoggedIn::class);

        $redux->changePassword('jBLUrUVXS8SM');
    }

    public function testChangePasswordLoggedIn()
    {
        $user = new \WBC\Auth\UserContainer('username', 'password_hash');

        $mock_storage = $this->mockStorage();
        $mock_storage->method('getUser')
            ->willReturn($user);
        $mock_storage->method('updatePassword');

        $mock_hash = $this->mockHash();
        $mock_hash->method('hash')
            ->willReturn('hashed');

        $mock_session = $this->mockSession();
        $mock_session->method('getUsername')
            ->willReturn($user->getUsername());

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);
        $redux->start();

        $redux->changePassword('jBLUrUVXS8SM');

        $this->assertEquals('hashed', $user->getPasswordHash());
    }

    public function testHash()
    {
        $mock_storage = $this->mockStorage();
        $mock_hash = $this->mockHash();
        $mock_hash->expects($this->once())
            ->method('hash')
            ->willReturn('hashed');
        $mock_session = $this->mockSession();

        $redux = new Redux($mock_storage, $mock_hash, $mock_session);
        $hashed = $redux->hash('abc');

        $this->assertEquals('hashed', $hashed);
    }
}