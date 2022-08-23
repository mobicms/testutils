<?php

declare(strict_types=1);

namespace Mobicms\Testutils;

use Mobicms\Testutils\Exception\MissingFileException;
use PDO;
use PDOException;

class SqlDumpLoader
{
    private PDO $pdo;
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

        $this->queryDb($this->splitSql($file));
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function splitSql(string $file): array
    {
        $query = trim(file_get_contents($file));
        preg_match_all('/([^;]*?((\'.*?\')|(".*?"))?)*?(;\s*|\s*$)/', $query, $matches); //NOSONAR

        return $matches[0];
    }

    private function queryDb(array $sql): void
    {
        /** @var string $query */
        foreach ($sql as $query) {
            $query = trim($query);

            if (! empty($query) && $query != '#') {
                try {
                    $this->pdo->query($query);
                } catch (PDOException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }
    }
}
