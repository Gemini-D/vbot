<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Console\QrCode;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['qrCode'] = function () use ($pimple) {
            return new QrCode($pimple);
        };
        $pimple['console'] = function () use ($pimple) {
            return new Console($pimple);
        };
    }
}
