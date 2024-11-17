<?php

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Testutils\MysqlTestCase;
use PHPUnit\Framework\TestCase;

class MysqlTestCaseTest extends TestCase
{
    private string $dbName;

    public function setUp(): void
    {
        $this->dbName = $GLOBALS['test_db_name'] ?? 'tmp_test_database';
        MysqlTestCase::setUpBeforeClass();
    }

    public function testCanCreateTemporaryDatabase(): void
    {
        $pdo = MysqlTestCase::getPdo();
        $result = $pdo
            ->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->dbName'")
            ->rowCount();
        self::assertEquals(1, $result);
    }

    public function testCanDestroyTemporaryDatabase(): void
    {
        MysqlTestCase::tearDownAfterClass();
        $pdo = MysqlTestCase::getPdo();
        $result = $pdo
            ->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->dbName'")
            ->rowCount();
        self::assertEquals(0, $result);
    }
}
