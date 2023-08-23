<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class RedPacket extends Message implements MessageInterface
{
    public const TYPE = 'red_packet';

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return $this->message;
    }
}
