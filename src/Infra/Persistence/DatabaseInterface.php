<?php

declare(strict_types=1);

namespace Orders\Infra\Persistence;

interface DatabaseInterface
{
    public function getPdo(): \PDO;
    
    public function createTables(): void;
}
