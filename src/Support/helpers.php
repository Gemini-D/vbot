<?php

declare(strict_types=1);

use Hanson\Vbot\Foundation\Vbot;

if (! function_exists('vbot')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     *
     * @return mixed|Vbot
     */
    function vbot($abstract = null)
    {
        if (is_null($abstract)) {
            return Vbot::getInstance();
        }

        return Vbot::getInstance()[$abstract];
    }
}
