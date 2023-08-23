<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Location extends Message implements MessageInterface
{
    public const TYPE = 'location';

    /**
     * 判断是否位置消息.
     *
     * @param mixed $content
     * @return bool
     */
    public static function isLocation($content)
    {
        return str_contains($content['Content'], 'webwxgetpubliclinkimg') && $content['Url'];
    }

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        if ($this->raw['FileName'] === '我发起了位置共享') {
            return '[共享位置]';
        }
        return current(explode(":\n", $this->message));
    }

    protected function getExpand(): array
    {
        return ['url' => $this->locationUrl()];
    }

    private function locationUrl()
    {
        return $this->raw['Url'];
    }
}
