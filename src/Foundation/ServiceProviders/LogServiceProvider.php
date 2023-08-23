<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Support\Log;
use Pimple\Container;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['log'] = function () {
            return new Log('vbot');
        };
        $pimple['messageLog'] = function () {
            return new Log('message');
        };
    }
}
