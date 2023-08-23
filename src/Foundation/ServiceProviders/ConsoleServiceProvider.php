<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Console\QrCode;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->bind('qrCode', function () use ($pimple) {
            return new QrCode($pimple);
        });
        $pimple->singleton('console', function () use ($pimple) {
            return new Console($pimple);
        });
    }
}
