<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Redis\RedisManager;
use Pimple\Container;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['files'] = new Filesystem();

        $pimple->singleton('cache', function ($vbot) {
            return new CacheManager($vbot);
        });
        $pimple->singleton('cache.store', function ($vbot) {
            return $vbot['cache']->driver();
        });
        $pimple->singleton('memcached.connector', function () {
            return new MemcachedConnector();
        });
        $pimple->singleton('redis', function ($vbot) {
            $config = $vbot->config['database.redis'];

            return new RedisManager(array_get($config, 'client', 'predis'), $config);
        });
        $pimple->bind('redis.connection', function ($vbot) {
            return $vbot['redis']->connection();
        });
    }
}
