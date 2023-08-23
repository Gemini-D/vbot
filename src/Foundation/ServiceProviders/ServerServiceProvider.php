<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Core\Swoole;
use Hanson\Vbot\Core\Sync;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->singleton('server', function () use ($pimple) {
            return new Server($pimple);
        });
        $pimple->singleton('swoole', function () use ($pimple) {
            return new Swoole($pimple);
        });
        $pimple->singleton('sync', function () use ($pimple) {
            return new Sync($pimple);
        });
    }
}
