<?php

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Testutils\ConfigLoader;
use PHPUnit\Framework\TestCase;

class ConfigLoaderTest extends TestCase
{
    public function testHostReturnsGlobalVariable(): void
    {
        $GLOBALS['test_db_host'] = 'test_host';
        $config = new ConfigLoader();
        self::assertSame('test_host', $config->host());
    }

    public function testHostReturnsDefaultVariable(): void
    {
        unset($GLOBALS['test_db_host']);
        $config = new ConfigLoader();
        self::assertSame('localhost', $config->host());
    }

    public function testPortReturnsGlobalVariable(): void
    {
        $GLOBALS['test_db_port'] = 9999;
        $config = new ConfigLoader();
        self::assertSame(9999, $config->port());
    }

    public function testPortReturnsDefaultVariable(): void
    {
        unset($GLOBALS['test_db_port']);
        $config = new ConfigLoader();
        self::assertSame(3306, $config->port());
    }

    public function testUserReturnsGlobalVariable(): void
    {
        $GLOBALS['test_db_user'] = 'test_user';
        $config = new ConfigLoader();
        self::assertSame('test_user', $config->user());
    }

    public function testUserReturnsDefaultVariable(): void
    {
        unset($GLOBALS['test_db_user']);
        $config = new ConfigLoader();
        self::assertSame('root', $config->user());
    }

    public function testPasswordReturnsGlobalVariable(): void
    {
        $GLOBALS['test_db_pass'] = 'test_pass';
        $config = new ConfigLoader();
        self::assertSame('test_pass', $config->password());
    }

    public function testPasswordReturnsDefaultVariable(): void
    {
        unset($GLOBALS['test_db_pass']);
        $config = new ConfigLoader();
        self::assertSame('root', $config->password());
    }

    public function testDbNameReturnsGlobalVariable(): void
    {
        $GLOBALS['test_db_name'] = 'test_name';
        $config = new ConfigLoader();
        self::assertSame('test_name', $config->dbName());
    }

    public function testDbNameReturnsDefaultVariable(): void
    {
        unset($GLOBALS['test_db_name']);
        $config = new ConfigLoader();
        self::assertSame('tmp_test_database', $config->dbName());
    }
}
