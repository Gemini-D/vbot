<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\MessageFactory;
use Hanson\Vbot\Core\MessageHandler;
use Hanson\Vbot\Core\ShareFactory;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['messageHandler'] = function () use ($pimple) {
            return new MessageHandler($pimple);
        };
        $pimple['messageFactory'] = function () use ($pimple) {
            return new MessageFactory($pimple);
        };
        $pimple['shareFactory'] = function () use ($pimple) {
            return new ShareFactory($pimple);
        };
    }
}
