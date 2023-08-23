<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\Cache\MemCache;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hyperf\Support\Filesystem\Filesystem;
use Illuminate\Redis\RedisManager;
use Pimple\Container;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['files'] = new Filesystem();
        $pimple['cache'] = function () {
            return new MemCache();
        };
        // $pimple->singleton('cache.store', function ($vbot) {
        //     return $vbot['cache']->driver();
        // });
        // $pimple->singleton('redis', function ($vbot) {
        //     $config = $vbot->config['database.redis'];
        //
        //     return new RedisManager(array_get($config, 'client', 'predis'), $config);
        // });
        // $pimple->bind('redis.connection', function ($vbot) {
        //     return $vbot['redis']->connection();
        // });
    }
}
