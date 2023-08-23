<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Log;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->singleton('log', function () {
            $log = new Log('vbot');

            return $log;
        });

        $pimple->singleton('messageLog', function () {
            $log = new Log('message');

            return $log;
        });
    }
}
