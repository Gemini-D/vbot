<?php

declare(strict_types=1);

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Foundation\VbotFactory;

if (! function_exists('vbot')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     *
     * @return mixed|Vbot
     */
    function vbot($abstract = null, int|string $id = 0)
    {
        if (is_null($abstract)) {
            return VbotFactory::get($id);
        }

        return VbotFactory::get($id)[$abstract];
    }
}
