<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use Mobicms\Testutils\Exception\MissingFileException;
use PDO;
use PDOException;

class SqlDumpLoader
{
    private PDO $pdo;

    /**
     * @var array<string>
     */
    private array $errors = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function loadFile(string $file): void
    {
        if (! is_file($file)) {
            throw new MissingFileException('Database dump not found: ' . $file);
        }

        /** @var string $query */
        foreach ($this->splitSql(file_get_contents($file)) as $query) {
            $this->queryDb(trim($query));
        }
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string>
     */
    private function splitSql(string $sql): array
    {
        return preg_split('~\([^)]*\)(*SKIP)(*F)|;~', $sql);
    }

    private function queryDb(string $query): void
    {
        if ($query !== '') {
            try {
                $this->pdo->query($query);
            } catch (PDOException $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }
}
