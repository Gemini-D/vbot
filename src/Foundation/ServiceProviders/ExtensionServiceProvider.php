<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Extension\MessageExtension;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['messageExtension'] = function () use ($pimple) {
            return new MessageExtension($pimple);
        };
    }
}
