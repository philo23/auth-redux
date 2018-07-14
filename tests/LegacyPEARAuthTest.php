<?php
namespace WBC\Tests\Auth;

use \WBC\Auth\LegacyPEARAuth;

class LegacyPEARAuthTest extends \PHPUnit\Framework\TestCase
{
    private function mockRedux ()
    {
        return $this->createMock(\WBC\Auth\Redux::class);
    }

    public function testConstructorBasic()
    {
        $mock_redux = $this->mockRedux();

        $inst = new LegacyPEARAuth($mock_redux);
        $this->assertNull($inst->loginFunction);
        $this->assertTrue($inst->showLogin);
    }

    public function testConstructorInvalidValues()
    {
        $mock_redux = $this->mockRedux();

        $inst = new LegacyPEARAuth($mock_redux, null, 'not a boolean');
        $this->assertNull($inst->loginFunction);
        $this->assertTrue($inst->showLogin);
    }

    public function testConstructorOldStyleCallback()
    {
        $mock_redux = $this->mockRedux();

        $inst = new LegacyPEARAuth($mock_redux, 'strtolower');
        $this->assertEquals('strtolower', $inst->loginFunction);
        $this->assertTrue($inst->showLogin);
    }

    public function testConstructorShowLogin()
    {
        $mock_redux = $this->mockRedux();

        $inst = new LegacyPEARAuth($mock_redux, null, true);
        $this->assertNull($inst->loginFunction);
        $this->assertTrue($inst->showLogin);

        $inst = new LegacyPEARAuth($mock_redux, null, false);
        $this->assertNull($inst->loginFunction);
        $this->assertFalse($inst->showLogin);
    }

    public function testCheckAuth()
    {
        $mock_redux = $this->mockRedux();
        $mock_redux->method('isLoggedIn')->willReturn(false);

        $inst = new LegacyPEARAuth($mock_redux);
        $this->assertFalse($inst->checkAuth());


        $mock_redux = $this->mockRedux();
        $mock_redux->method('isLoggedIn')->willReturn(true);

        $inst = new LegacyPEARAuth($mock_redux);
        $this->assertTrue($inst->checkAuth());
    }

    public function testCheckAuthWithCallback()
    {
        $abort_callback = function () { return false; };

        $mock_redux = $this->mockRedux();
        $mock_redux->method('isLoggedIn')->willReturn(true);

        $inst = new LegacyPEARAuth($mock_redux);
        $inst->setCheckAuthCallback($abort_callback);
        $this->assertFalse($inst->checkAuth());
        $this->assertEquals(LegacyPEARAuth::CALLBACK_ABORT, $inst->getStatus());

        $mock_redux = $this->mockRedux();
        $mock_redux->method('isLoggedIn')->willReturn(false);

        $inst = new LegacyPEARAuth($mock_redux);
        $inst->setCheckAuthCallback($abort_callback);
        $this->assertFalse($inst->checkAuth());
        $this->assertEmpty($inst->getStatus());
    }

    public function testConstants()
    {
        $this->assertFalse(defined('AUTH_WRONG_LOGIN'));
        $this->assertTrue(defined(LegacyPEARAuth::class . '::WRONG_LOGIN'));

        LegacyPEARAuth::defineLegacyConstants();

        $this->assertTrue(defined('AUTH_WRONG_LOGIN'));
        $this->assertTrue(defined(LegacyPEARAuth::class . '::WRONG_LOGIN'));
    }
}