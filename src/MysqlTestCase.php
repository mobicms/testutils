<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-api
 */
class MysqlTestCase extends TestCase
{
    private static ConfigLoader $config;
    private static PDO $pdo;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        $config = new ConfigLoader();
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d', $config->host(), $config->port()),
            $config->user(),
            $config->password(),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4', $config->dbName()));
        $pdo->exec(sprintf('USE %s', $config->dbName()));

        self::$config = $config;
        self::$pdo = $pdo;
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP DATABASE IF EXISTS ' . self::$config->dbName());
    }

    public static function getPdo(): PDO
    {
        return self::$pdo;
    }
}
