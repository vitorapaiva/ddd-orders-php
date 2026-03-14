<?php

declare(strict_types=1);

namespace Orders\Domain\Validation;

final class ValidationHelper
{
    public static function requireKeys(array $data, array $keys, string $context = 'data'): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException("{$context} missing required field: {$key}");
            }
        }
    }

    public static function requireNonEmpty(array $data, string $key, string $message): void
    {
        if (!isset($data[$key]) || (empty($data[$key]) && $data[$key] !== '0')) {
            throw new \InvalidArgumentException($message);
        }
    }

    public static function requireNonEmptyArray(array $data, string $key, string $message): void
    {
        if (!is_array($data[$key] ?? null) || empty($data[$key])) {
            throw new \InvalidArgumentException($message);
        }
    }

    public static function requireArray(array $data, string $key, string $message): void
    {
        if (!isset($data[$key]) || !is_array($data[$key])) {
            throw new \InvalidArgumentException($message);
        }
    }
}
