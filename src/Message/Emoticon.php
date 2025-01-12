<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;
use Hanson\Vbot\Support\File;

class Emoticon extends Message implements MessageInterface
{
    use SendAble;
    use Multimedia;

    public const API = 'webwxsendemoticon?fun=sys&f=json&';

    public const DOWNLOAD_API = 'webwxgetmsgimg?&MsgID=';

    public const EXT = '.gif';

    public const TYPE = 'emoticon';

    public function make($msg, int|string $id = 0)
    {
        static::autoDownload($msg, id: $id);
        static::downloadToLibrary($msg, $id);

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
            'Type' => 47,
            'EmojiFlag' => 2,
            'MediaId' => $response['MediaId'],
            'FromUserName' => vbot('myself', $id)->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    /**
     * 从本地表�
     * 库随机发送一个.
     *
     * @param mixed $username
     */
    public static function sendRandom($username, int|string $id = 0): bool
    {
        if (! is_dir($path = vbot('config', $id)['download.emoticon_path'])) {
            vbot('console', $id)->log('emoticon path not set.', Console::WARNING);

            return false;
        }

        $files = scandir($path);
        unset($files[0], $files[1]);

        if (count($files)) {
            $msgId = $files[array_rand($files)];

            static::send($username, $path . DIRECTORY_SEPARATOR . $msgId, $id);
            return true;
        }

        return false;
    }

    protected function parseToContent(): string
    {
        return '[表情]';
    }

    private static function downloadToLibrary($message, int|string $id = 0)
    {
        if (! vbot('config', $id)['download.emoticon_path']) {
            return false;
        }

        if (is_file($path = vbot('config', $id)['user_path'] . static::TYPE . DIRECTORY_SEPARATOR . $message['MsgId'] . static::EXT)) {
            static::copyFromEmoticon($path, $id);
        } else {
            static::saveFromApi($message, $id);
        }
    }

    private static function copyFromEmoticon($path, int|string $id = 0)
    {
        $target = vbot('config', $id)['download.emoticon_path'] . DIRECTORY_SEPARATOR;

        if (! static::isExist($md5 = md5_file($path), $id)) {
            copy($path, $target . $md5 . static::EXT);
        }
    }

    private static function saveFromApi($message, int|string $id = 0)
    {
        $target = vbot('config', $id)['download.emoticon_path'] . DIRECTORY_SEPARATOR;

        $resource = static::getResource($message, $id);

        $fileName = $target . 'tmp-' . time() . rand() . static::EXT;

        File::saveTo($fileName, $resource);

        $md5 = md5_file($fileName);
        copy($fileName, $target . $md5 . static::EXT);
        unlink($fileName);
    }

    private static function isExist($md5, int|string $id = 0)
    {
        return is_file(vbot('config', $id)['download.emoticon_path'] . DIRECTORY_SEPARATOR . $md5 . static::EXT);
    }
}
