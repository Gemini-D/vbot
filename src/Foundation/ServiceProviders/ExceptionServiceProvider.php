<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ExceptionHandler;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Pimple\Container;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Vbot $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['exception'] = function () use ($pimple) {
            return new ExceptionHandler($pimple);
        };
    }
}
