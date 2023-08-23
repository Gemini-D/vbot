<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Touch extends Message implements MessageInterface
{
    public const TYPE = 'touch';

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[点击事件]';
    }
}
