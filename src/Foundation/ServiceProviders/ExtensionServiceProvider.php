<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Extension\MessageExtension;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->singleton('messageExtension', function () use ($pimple) {
            return new MessageExtension($pimple);
        });
    }
}
