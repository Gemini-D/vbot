<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Core\Sync;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['server'] = function () use ($pimple) {
            return new Server($pimple);
        };
        $pimple['sync'] = function () use ($pimple) {
            return new Sync($pimple);
        };
    }
}
