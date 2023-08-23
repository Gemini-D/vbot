<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Api\ApiHandler;
use Hanson\Vbot\Api\Search;
use Hanson\Vbot\Api\Send;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Pimple\Container;

class ApiServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $pimple
     */
    public function register(Container $pimple)
    {
        $pimple->singleton('api', function () use ($pimple) {
            return new ApiHandler($pimple);
        });
        $pimple->singleton('apiSend', function () use ($pimple) {
            return new Send($pimple);
        });
        $pimple->singleton('apiSearch', function () use ($pimple) {
            return new Search($pimple);
        });
    }
}
