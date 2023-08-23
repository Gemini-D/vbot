<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core\Cache;

use Psr\SimpleCache\CacheInterface;

class SimpleCache
{
    public function __construct(protected CacheInterface $cache)
    {
    }

    public function forget(string $key): void
    {
        $this->cache->delete($key);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function forever(string $key, mixed $data): void
    {
        $this->cache->set($key, $data);
    }
}
