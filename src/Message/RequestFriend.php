<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class RequestFriend extends Message implements MessageInterface
{
    public const TYPE = 'request_friend';

    /**
     * @var array 信息
     */
    private $info;

    private $avatar;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $this->info = $this->raw['RecommendInfo'];
        $isMatch = preg_match('/bigheadimgurl="(.+?)"/', $this->message, $matches);

        if ($isMatch) {
            $this->avatar = $matches[1];
        }
    }

    protected function getExpand(): array
    {
        return ['info' => $this->info, 'avatar' => $this->avatar];
    }

    protected function parseToContent(): string
    {
        return '[请求添加好友]';
    }
}
