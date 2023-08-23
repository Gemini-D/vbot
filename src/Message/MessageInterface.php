<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

interface MessageInterface
{
    public function make($msg);
}
