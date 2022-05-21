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

    public function loadSqlDump(string $file): void
    {
        if (file_exists($file)) {
            /** @var array<string> $errors */
            $errors = $this->parseSql($file, self::$pdo);

            if (! empty($errors)) {
                echo 'SQL File ' . $file . ' following errors occurred:' . "\n";
                echo "\n" . implode("\n", $errors) . "\n";
            }
        } else {
            throw new \RuntimeException('Database dump not found: ' . $file);
        }
    }

    /**
     * @param string $file
     * @param PDO $pdo
     * @return array
     *
     * @psalm-suppress LoopInvalidation
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    private function parseSql(string $file, PDO $pdo): array
    {
        $errors = [];

        $query = fread(fopen($file, 'r'), filesize($file));
        $query = trim($query);
        $query = preg_replace("/\n#[^\n]*/", '', "\n" . $query);
        $buffer = [];
        $ret = [];
        $in_string = false;

        for ($i = 0; $i < strlen($query) - 1; $i++) {
            if ($query[$i] == ';' && ! $in_string) {
                $ret[] = substr($query, 0, $i);
                $query = substr($query, $i + 1);
                $i = 0;
            }
            if ($in_string && ($query[$i] == $in_string) && $buffer[1] != '\\') {
                $in_string = false;
            } elseif (
                ! $in_string
                && ($query[$i] == '"' || $query[$i] == "'")
                && (! isset($buffer[0]) || $buffer[0] != '\\')
            ) {
                $in_string = $query[$i];
            }
            if (isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $query[$i];
        }
        if (! empty($query)) {
            $ret[] = $query;
        }
        for ($i = 0; $i < count($ret); $i++) {
            $ret[$i] = trim($ret[$i]);
            if (! empty($ret[$i]) && $ret[$i] != '#') {
                try {
                    $pdo->query($ret[$i]);
                } catch (PDOException $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        return $errors;
    }
}
