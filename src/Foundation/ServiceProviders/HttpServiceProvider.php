<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Http;
use Pimple\Container;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['http'] = function () use ($pimple) {
            return new Http($pimple);
        };
    }
}
