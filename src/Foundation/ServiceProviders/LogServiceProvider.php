<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Log;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->singleton('log', function () {
            return new Log('vbot');
        });

        $pimple->singleton('messageLog', function () {
            return new Log('message');
        });
    }
}
