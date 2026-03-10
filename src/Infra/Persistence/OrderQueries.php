<?php

declare(strict_types=1);

namespace Orders\Infra\Persistence;

final class OrderQueries
{
    public const INSERT = <<<SQL
        INSERT INTO orders 
        (id, customer_id, shipping_address, billing_address, items, total, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    SQL;

    public const FIND_BY_ID = 'SELECT * FROM orders WHERE id = ?';

    public const UPDATE_STATUS = 'UPDATE orders SET status = ?, updated_at = ? WHERE id = ?';

    public const LIST_ALL = 'SELECT * FROM orders ORDER BY created_at DESC';
}
