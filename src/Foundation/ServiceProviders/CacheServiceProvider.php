<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\Cache\MemCache;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hyperf\Support\Filesystem\Filesystem;
use Pimple\Container;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['files'] = new Filesystem();
        $pimple['cache'] = function () {
            return new MemCache();
        };
    }
}
