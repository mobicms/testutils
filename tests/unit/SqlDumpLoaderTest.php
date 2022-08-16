<?php

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Testutils\SqlDumpLoader;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SqlDumpLoaderTest extends TestCase
{
    private string $dbName = 'testutils_tmp_db';
    private SqlDumpLoader $loader;
    private PDO $pdo;

    public function setUp(): void
    {
        $this->pdo = $this->connect();
        $this->pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4', $this->dbName));
        $this->pdo->exec(sprintf('USE %s', $this->dbName));

        $this->loader = new SqlDumpLoader($this->pdo);
    }

    public function tearDown(): void
    {
        $this->pdo->exec('DROP DATABASE IF EXISTS ' . $this->dbName);
    }

    public function testLoadSqlFile(): void
    {
        $this->loader->loadFile(__DIR__ . '/../stubs/test.sql');
        $result = $this->pdo->query('SELECT * FROM `test`')->fetchAll();

        $this->assertFalse($this->loader->hasErrors());
        $this->assertEquals('foo', $result[0][1]);
        $this->assertEquals('bar', $result[1][1]);
        $this->assertEquals('baz', $result[2][1]);
    }

    public function testThrowExceptionOnMissingSqlFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->loader->loadFile('invalid_file');
    }

    public function testHasErrors()
    {
        $this->loader->loadFile(__DIR__ . '/../stubs/test.sql');
        $this->assertFalse($this->loader->hasErrors());

        $this->loader->loadFile(__DIR__ . '/../stubs/test_errors.sql');
        $this->assertTrue($this->loader->hasErrors());
    }

    public function testGetErrors()
    {
        $this->loader->loadFile(__DIR__ . '/../stubs/test_errors.sql');
        $errors = $this->loader->getErrors();
        $this->assertIsArray($errors);
        $this->assertStringContainsString('SQLSTATE[42S02]', $errors[0]);
    }

    private function connect(): PDO
    {
        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d',
                (defined('TEST_DB_HOST') ? TEST_DB_HOST : 'localhost'),
                (defined('TEST_DB_PORT') ? TEST_DB_PORT : 3306)
            ),
            (defined('TEST_DB_USER') ? TEST_DB_USER : 'root'),
            (defined('TEST_DB_PASS') ? TEST_DB_PASS : 'root')
        );
    }
}
