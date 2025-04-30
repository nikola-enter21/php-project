<?php

namespace Core;

use Exception;
use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    /**
     * @throws Exception
     */
    public function __construct(string $host, string $dbname, string $user, string $password, int $port = 5432)
    {
        try {
            $this->pdo = new PDO(
                "pgsql:host={$host};port={$port};dbname={$dbname}",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Failed to connect to the database.");
        }
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function querySingle(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}