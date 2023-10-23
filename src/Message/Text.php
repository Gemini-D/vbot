<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\SendAble;

class Text extends Message implements MessageInterface
{
    use SendAble;

    public const TYPE = 'text';

    public const API = 'webwxsendmsg?';

    private $isAt = false;

    private $pure;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    /**
     * send a text message.
     *
     * @param mixed $username
     * @param mixed $word
     * @return bool|mixed
     */
    public static function send(int|string $id, $username, $word)
    {
        if (! $word || ! $username) {
            return false;
        }

        return static::sendMsg([
            'Type' => 1,
            'Content' => $word,
            'FromUserName' => vbot('myself', $id)->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $this->isAt = str_contains($this->message, '@' . vbot('myself', $id)->nickname);
        $this->pure = $this->pureText();
    }

    protected function getExpand(): array
    {
        return ['isAt' => $this->isAt, 'pure' => $this->pure];
    }

    protected function parseToContent(): string
    {
        return $this->message;
    }

    private function pureText()
    {
        $content = str_replace(' ', ' ', $this->message);
        $isMatch = preg_match('/^@(.+?)\s([\s\S]*)/', $content, $match);

        return $isMatch ? $match[2] : $this->message;
    }
}
