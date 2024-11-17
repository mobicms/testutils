<?php

declare(strict_types=1);

namespace MobicmsTest;

use Mobicms\Testutils\Exception\MissingFileException;
use Mobicms\Testutils\SqlDumpLoader;
use PDO;
use PHPUnit\Framework\TestCase;

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

        self::assertFalse($this->loader->hasErrors());
        self::assertEquals('foo;', $result[0][1]);
        self::assertEquals('bar', $result[1][1]);
        self::assertEquals('baz', $result[2][1]);
    }

    public function testThrowExceptionOnMissingSqlFile(): void
    {
        $this->expectException(MissingFileException::class);
        $this->loader->loadFile('invalid_file');
    }

    public function testHasErrors(): void
    {
        $this->loader->loadFile(__DIR__ . '/../stubs/test.sql');
        self::assertFalse($this->loader->hasErrors());

        $this->loader->loadFile(__DIR__ . '/../stubs/test_errors.sql');
        self::assertTrue($this->loader->hasErrors());
    }

    public function testGetErrors(): void
    {
        $this->loader->loadFile(__DIR__ . '/../stubs/test_errors.sql');
        $errors = $this->loader->getErrors();
        self::assertStringContainsString('SQLSTATE[42S02]', $errors[0]);
    }

    private function connect(): PDO
    {
        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d',
                ($GLOBALS['test_db_host'] ?? 'localhost'),
                ($GLOBALS['test_db_port'] ?? 3306)
            ),
            ($GLOBALS['test_db_user'] ?? 'root'),
            ($GLOBALS['test_db_pass'] ?? 'root')
        );
    }
}
