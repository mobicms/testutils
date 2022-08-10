<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use PDO;
use PHPUnit\Framework\TestCase;

class MysqlTestCase extends TestCase
{
    private static int $dbPort = 3306;
    private static string $dbHost = 'localhost';
    private static string $dbName = 'tmp_test_database';
    private static string $dbUser = 'root';
    private static string $dbPass = 'root';

    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::fetchConfig();
        self::$pdo = new PDO(
            sprintf('mysql:host=%s;port=%d', self::$dbHost, self::$dbPort),
            self::$dbUser,
            self::$dbPass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        self::$pdo->exec(
            'CREATE DATABASE IF NOT EXISTS ' . self::$dbName .
            ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;' .
            'USE ' . self::$dbName
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP DATABASE IF EXISTS ' . self::$dbName);
    }

    public static function getPdo(): PDO
    {
        return self::$pdo;
    }

    private static function fetchConfig(): void
    {
        if (defined('TEST_DB_HOST')) {
            self::$dbHost = (string) TEST_DB_HOST;
        }

        if (defined('TEST_DB_PORT')) {
            self::$dbPort = (int) TEST_DB_PORT;
        }

        if (defined('TEST_DB_NAME')) {
            self::$dbName = (string) TEST_DB_NAME;
        }

        if (defined('TEST_DB_USER')) {
            self::$dbUser = (string) TEST_DB_USER;
        }

        if (defined('TEST_DB_PASS')) {
            self::$dbPass = (string) TEST_DB_PASS;
        }
    }
}
