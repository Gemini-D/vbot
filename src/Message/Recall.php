<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Recall extends Message implements MessageInterface
{
    public const TYPE = 'recall';

    private $nickname;

    private $origin;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $msgId = $this->parseMsgId($this->message);

        $this->origin = vbot('cache', $id)->get('msg-' . $msgId);

        if ($this->origin) {
            $this->nickname = $this->origin['sender'] ?
                $this->origin['sender']['NickName'] :
                vbot('contacts', $id)->getAccount($this->origin['raw']['FromUserName'])['NickName'];
        }
    }

    protected function getExpand(): array
    {
        return ['origin' => $this->origin, 'nickname' => $this->nickname];
    }

    protected function parseToContent(): string
    {
        return $this->nickname . ' 刚撤回了消息';
    }

    /**
     * 解析message获取msgId.
     *
     * @param mixed $xml
     * @return string msgId
     */
    private function parseMsgId($xml)
    {
        preg_match('/<msgid>(\d+)<\/msgid>/', $xml, $matches);

        return $matches[1];
    }
}
