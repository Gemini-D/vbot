<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core\Cache;

class MemCache
{
    protected array $data = [];

    public function forget(string $key): void
    {
        unset($this->data[$key]);
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function forever(string $key, mixed $data): void
    {
        $this->data[$key] = $data;
    }
}
