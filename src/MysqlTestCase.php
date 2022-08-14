<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use PDO;
use PHPUnit\Framework\TestCase;

class MysqlTestCase extends TestCase
{
    private static Config $config;
    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$config = new Config();
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d', self::$config->host(), self::$config->port()),
            self::$config->user(),
            self::$config->password(),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4', self::$config->dbName()));
        $pdo->exec(sprintf('USE %s', self::$config->dbName()));
        self::$pdo = $pdo;
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP DATABASE IF EXISTS ' . self::$config->dbName());
    }

    public static function getPdo(): PDO
    {
        return self::$pdo;
    }
}
