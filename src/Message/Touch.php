<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Touch extends Message implements MessageInterface
{
    public const TYPE = 'touch';

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function parseToContent(): string
    {
        return '[点击事件]';
    }
}
