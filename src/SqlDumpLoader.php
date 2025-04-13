<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use Mobicms\Testutils\Exception\MissingFileException;
use PDO;
use PDOException;

/**
 * @psalm-api
 */
final class SqlDumpLoader
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

        $content = file_get_contents($file);

        if ($content !== false) {
            foreach ($this->splitSql($content) as $query) {
                $this->queryDb(trim($query));
            }
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
        $split = preg_split('~\([^)]*\)(*SKIP)(*F)|;~', $sql);

        if ($split === false) {
            throw new \RuntimeException('Unable to split SQL');
        }

        return $split;
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
