<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;

class Video extends Message implements MessageInterface
{
    use SendAble;
    use Multimedia;

    public const API = 'webwxsendvideomsg?fun=async&f=json&';

    public const DOWNLOAD_API = 'webwxgetvideo?msgid=';

    public const EXT = '.mp4';

    public const TYPE = 'video';

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

        $response = static::uploadVideo($username, $file, $id);

        return static::sendMsg([
            'Type' => 43,
            'MediaId' => $response['MediaId'],
            'FromUserName' => vbot('myself')->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    protected function parseToContent(): string
    {
        return '[视频]';
    }

    protected static function getDownloadOption()
    {
        return ['headers' => ['Range' => 'bytes=0-']];
    }
}
