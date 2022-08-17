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
        return (string) ($GLOBALS['test_db_host'] ?? $this->defaultHost);
    }

    public function port(): int
    {
        return (int) ($GLOBALS['test_db_port'] ?? $this->defaultPort);
    }

    public function user(): string
    {
        return (string) ($GLOBALS['test_db_user'] ?? $this->defaultUser);
    }

    public function password(): string
    {
        return (string) ($GLOBALS['test_db_pass'] ?? $this->defaultPassword);
    }

    public function dbName(): string
    {
        return (string) ($GLOBALS['test_db_name'] ?? $this->defaultDbName);
    }
}
