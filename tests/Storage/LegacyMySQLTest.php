<?php
namespace WBC\Tests\Auth\Storage;

use WBC\Auth\Storage\LegacyMySQL;

class LegacyMySQLTest extends \PHPUnit\Framework\TestCase
{
    public function testNullDatabase()
    {
        try {
            $storage = new LegacyMySQL(null);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    public function testInvalidDatabase()
    {
        $this->expectException(\InvalidArgumentException::class);
        $storage = new LegacyMySQL('abc');
    }
}
