<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

class ConfigLoader
{
    private int $defaultPort = 3306;
    private string $defaultHost = 'localhost';
    private string $defaultUser = 'root';
    private string $defaultPassword = 'root';
    private string $defaultDbName = 'tmp_test_database';

    public function host(): string
    {
        return defined('TEST_DB_HOST')
            ? (string) TEST_DB_HOST
            : $this->defaultHost;
    }

    public function port(): int
    {
        return defined('TEST_DB_PORT')
            ? (int) TEST_DB_PORT
            : $this->defaultPort;
    }

    public function user(): string
    {
        return defined('TEST_DB_USER')
            ? (string) TEST_DB_USER
            : $this->defaultUser;
    }

    public function password(): string
    {
        return defined('TEST_DB_PASS')
            ? (string) TEST_DB_PASS
            : $this->defaultPassword;
    }

    public function dbName(): string
    {
        return defined('TEST_DB_NAME')
            ? (string) TEST_DB_NAME
            : $this->defaultDbName;
    }
}
