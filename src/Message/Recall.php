<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Recall extends Message implements MessageInterface
{
    public const TYPE = 'recall';

    private $nickname;

    private $origin;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $msgId = $this->parseMsgId($this->message);

        $this->origin = vbot('cache')->get('msg-' . $msgId);

        if ($this->origin) {
            $this->nickname = $this->origin['sender'] ?
                $this->origin['sender']['NickName'] :
                vbot('contacts')->getAccount($this->origin['raw']['FromUserName'])['NickName'];
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
     * @return string msgId
     */
    private function parseMsgId($xml)
    {
        preg_match('/<msgid>(\d+)<\/msgid>/', $xml, $matches);

        return $matches[1];
    }
}
