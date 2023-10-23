<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Exceptions\ArgumentException;

class ApiExceptionHandler
{
    public static function handle($bag, $callback = null, int|string $id = 0)
    {
        if ($callback && ! is_callable($callback)) {
            throw new ArgumentException();
        }

        if ($bag['BaseResponse']['Ret'] != 0) {
            if ($callback) {
                call_user_func_array($callback, $bag);
            }
        }

        switch ($bag['BaseResponse']['Ret']) {
            case 1:
                vbot('console', $id)->log('Argument pass error.', Console::WARNING, ['id' => $id]);
                break;
            case -14:
                vbot('console', $id)->log('Ticket error.', Console::WARNING, ['id' => $id]);
                break;
            case 1101:
            case 1100:
                vbot('console', $id)->log('Logout.', Console::WARNING, ['id' => $id]);
                break;
            case 1102:
                vbot('console', $id)->log('Cookies invalid.', Console::WARNING, ['id' => $id]);
                break;
            case 1105:
                vbot('console', $id)->log('Api frequency.', Console::WARNING, ['id' => $id]);
                break;
        }

        return $bag;
    }
}
