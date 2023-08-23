<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ExceptionHandler;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['exception'] = function () use ($pimple) {
            return new ExceptionHandler($pimple);
        };
    }
}
