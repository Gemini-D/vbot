<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;

class Image extends Message implements MessageInterface
{
    use Multimedia;
    use SendAble;

    public const API = 'webwxsendmsgimg?fun=async&f=json&';

    public const DOWNLOAD_API = 'webwxgetmsgimg?&MsgID=';

    public const EXT = '.jpg';

    public const TYPE = 'image';

    public function make($msg, int|string $id = 0)
    {
        static::autoDownload($msg, id: $id);

        return $this->getCollection($msg, static::TYPE, $id);
    }

    public static function send(int|string $id, $username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw'], $id);

        if (! is_file($file)) {
            return false;
        }

        $response = static::uploadMedia($id, $username, $file);

        return static::sendMsg([
            'Type' => 3,
            'MediaId' => $response['MediaId'],
            'FromUserName' => vbot('myself', $id)->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    protected function parseToContent(): string
    {
        return '[图片]';
    }
}
