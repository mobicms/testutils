<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

class MysqlTestCase extends TestCase
{
    protected static string $dbHost;
    protected static int $dbPort;
    protected static string $dbName;
    protected static string $dbUser;
    protected static string $dbPass;

    protected static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$dbHost = (string) (defined('TEST_DB_HOST') ? TEST_DB_HOST : 'localhost');
        self::$dbPort = (int) (defined('TEST_DB_PORT') ? TEST_DB_PORT : 3306);
        self::$dbUser = (string) (defined('TEST_DB_USER') ? TEST_DB_USER : 'root');
        self::$dbPass = (string) (defined('TEST_DB_PASS') ? TEST_DB_PASS : 'root');
        self::$dbName = (string) (defined('TEST_DB_NAME') ? TEST_DB_NAME : 'tmp_test_database');

        self::$pdo = self::connect();
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec('DROP DATABASE IF EXISTS ' . self::$dbName);
    }

    private static function connect(): PDO
    {
        try {
            $pdo = new PDO(
                'mysql:host=' . self::$dbHost . ';port=' . self::$dbPort,
                self::$dbUser,
                self::$dbPass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            $pdo->exec(
                'CREATE DATABASE IF NOT EXISTS ' . self::$dbName .
                ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;' .
                'USE ' . self::$dbName
            );

            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException("\n\e[31m" . ' PDO EXCEPTION: ' . "\e[0m " . $e->getMessage() . "\n");
        }
    }
}
