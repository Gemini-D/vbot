<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Carbon\Carbon;
use Hanson\Vbot\Support\Content;
use Hyperf\Codec\Json;
use Hyperf\Collection\Collection;

abstract class Message
{
    public const FROM_TYPE_SYSTEM = 'System';

    public const FROM_TYPE_SELF = 'Self';

    public const FROM_TYPE_GROUP = 'Group';

    public const FROM_TYPE_FRIEND = 'Friend';

    public const FROM_TYPE_OFFICIAL = 'Official';

    public const FROM_TYPE_SPECIAL = 'Special';

    public const FROM_TYPE_UNKNOWN = 'Unknown';

    /**
     * @var ?array 消息来源
     */
    public ?array $from = null;

    /**
     * @var ?array 当from为群组时，sender为用户发送�
     */
    public ?array $sender = null;

    /**
     * 发送�
     * username.
     */
    public $username;

    /**
     * @var string 经处理的�
     *             容 （与类型无�
     *             � 有可能是一串xml）
     */
    public $message;

    /**
     * @var Carbon 时间
     */
    public $time;

    /**
     * @var string 消息发送�
     *             类型
     */
    public $fromType;

    /**
     * @var array 原始数据
     */
    public $raw;

    public function __toString()
    {
        return Json::encode($this->raw);
    }

    protected function create($msg, int|string $id = 0): array
    {
        $this->raw = $msg;

        $this->setFrom($id);
        $this->setFromType($id);
        $this->setMessage($id);
        $this->setTime();
        $this->setUsername();

        return [
            'raw' => $this->raw, 'from' => $this->from, 'fromType' => $this->fromType, 'sender' => $this->sender,
            'message' => $this->message, 'time' => $this->time, 'username' => $this->username,
        ];
    }

    protected function getCollection($msg, $type, int|string $id = 0)
    {
        $origin = $this->create($msg, $id);

        $this->afterCreate($id);

        $result = array_merge($origin, [
            'content' => $this->parseToContent(),
            'type' => $type,
        ], $this->getExpand());

        return new Collection($result);
    }

    protected function afterCreate(int|string $id = 0)
    {
    }

    protected function getExpand(): array
    {
        return [];
    }

    abstract protected function parseToContent(): string;

    /**
     * 设置消息发送�
     * .
     */
    private function setFrom(int|string $id = 0)
    {
        $this->from = vbot('contacts', $id)->getAccount($this->raw['FromUserName']);
    }

    private function setFromType(int|string $id = 0)
    {
        if ($this->raw['MsgType'] == 51) {
            $this->fromType = self::FROM_TYPE_SYSTEM;
        } elseif ($this->raw['FromUserName'] === vbot('myself', $id)->username) {
            $this->fromType = self::FROM_TYPE_SELF;
            $this->from = vbot('friends', $id)->getAccount($this->raw['ToUserName']);
        } elseif (vbot('groups', $id)->isGroup($this->raw['FromUserName'])) { // group
            $this->fromType = self::FROM_TYPE_GROUP;
        } elseif (vbot('friends', $id)->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_FRIEND;
        } elseif (vbot('officials', $id)->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_OFFICIAL;
        } elseif (vbot('specials', $id)->get($this->raw['FromUserName'], false)) {
            $this->fromType = self::FROM_TYPE_SPECIAL;
        } else {
            $this->fromType = self::FROM_TYPE_UNKNOWN;
        }
    }

    private function setMessage(int|string $id = 0)
    {
        $this->message = Content::formatContent($this->raw['Content']);

        if ($this->fromType === self::FROM_TYPE_GROUP) {
            $this->handleGroupContent($id);
        }
    }

    private function setUsername()
    {
        if ($this->fromType === 'Group' && $this->sender) {
            $this->username = $this->sender['UserName'];
        } elseif ($this->from) {
            $this->username = $this->from['UserName'];
        }
    }

    /**
     * 处理群发消息的�
     * 容.
     */
    private function handleGroupContent(int|string $id = 0)
    {
        $content = $this->message;

        if (! $content || ! str_contains($content, ":\n")) {
            return;
        }

        [$uid, $content] = explode(":\n", $content, 2);

        $this->sender = vbot('contacts', $id)->getAccount($uid) ?: vbot('groups', $id)->getMemberByUsername($this->raw['FromUserName'], $uid);
        $this->message = Content::replaceBr($content);
    }

    private function setTime()
    {
        $this->time = Carbon::createFromTimestamp($this->raw['CreateTime']);
    }
}
