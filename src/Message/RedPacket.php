<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class RedPacket extends Message implements MessageInterface
{
    public const TYPE = 'red_packet';

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function parseToContent(): string
    {
        return $this->message;
    }
}
