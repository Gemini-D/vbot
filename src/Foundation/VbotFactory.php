<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation;

use Hyperf\Coroutine\Locker;

class VbotFactory
{
    /**
     * @var array<int|string, Vbot>
     */
    protected static $instances = [];

    public static function get(int|string $id = 0): ?Vbot
    {
        return static::$instances[$id] ?? null;
    }

    public static function has(int|string $id = 0): bool
    {
        return array_key_exists($id, static::$instances);
    }

    public static function set(int|string $id, Vbot $vbot): Vbot
    {
        return static::$instances[$id] = $vbot;
    }

    public static function getOrSet(int|string $id, callable $callable): Vbot
    {
        if (! static::has($id)) {
            $key = VbotFactory::class . '::' . $id;
            if (Locker::lock($key)) {
                static::set($id, $callable($id));
                Locker::unlock($key);
            }
        }

        return static::get($id);
    }
}
