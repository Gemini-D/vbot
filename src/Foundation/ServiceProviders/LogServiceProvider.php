<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hyperf\Logger\LoggerFactory;
use Pimple\Container;

use function Hanson\Vbot\Support\app;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['log'] = function () {
            return app()->get(LoggerFactory::class)->get('vbot');
        };
        $pimple['messageLog'] = function () {
            return app()->get(LoggerFactory::class)->get('vbot.message');
        };
    }
}
