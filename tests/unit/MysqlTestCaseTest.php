<?php

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Testutils\MysqlTestCase;
use PDO;
use PHPUnit\Framework\TestCase;

class MysqlTestCaseTest extends TestCase
{
    private string $dbName;

    public function setUp(): void
    {
        $this->dbName = defined('TEST_DB_NAME') ? TEST_DB_NAME : '';
        MysqlTestCase::setUpBeforeClass();
    }

    public function testCanGetPdoInstance(): void
    {
        $pdo = MysqlTestCase::getPdo();
        $this->assertInstanceOf(PDO::class, $pdo);
    }

    public function testCanCreateTemporaryDatabase(): void
    {
        $pdo = MysqlTestCase::getPdo();
        $result = $pdo
            ->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->dbName'")
            ->rowCount();
        $this->assertEquals(1, $result);
    }

    public function testCanDestroyTemporaryDatabase(): void
    {
        MysqlTestCase::tearDownAfterClass();
        $pdo = MysqlTestCase::getPdo();
        $result = $pdo
            ->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->dbName'")
            ->rowCount();
        $this->assertEquals(0, $result);
    }
}
