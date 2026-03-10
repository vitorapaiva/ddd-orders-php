<?php

declare(strict_types=1);

namespace Orders\Infra\Persistence;

class Database implements DatabaseInterface
{
    private \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        $this->pdo = new \PDO($dsn, $config['user'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function createTables(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS orders (
                id VARCHAR(36) PRIMARY KEY,
                customer_id VARCHAR(36) NOT NULL,
                shipping_address JSON NOT NULL,
                billing_address JSON NOT NULL,
                items JSON NOT NULL,
                total DECIMAL(10, 2) NOT NULL,
                status VARCHAR(50) NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                INDEX idx_customer_id (customer_id),
                INDEX idx_status (status)
            )
        ";
        $this->pdo->exec($sql);
    }
}
