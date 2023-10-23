<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;

class File extends Message implements MessageInterface
{
    use Multimedia;
    use SendAble;

    public const API = 'webwxsendappmsg?fun=async&f=json&';

    public const DOWNLOAD_API = 'webwxgetmedia';

    public const TYPE = 'file';

    private $title;

    public function make($msg, int|string $id = 0)
    {
        static::autoDownload($msg);

        return $this->getCollection($msg, static::TYPE, $id);
    }

    public static function send(int|string $id, $username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw'], $id);

        $response = static::uploadMedia($id, $username, $file);

        $mediaId = $response['MediaId'];

        $explode = explode('.', $file);
        $fileName = end($explode);

        return static::sendMsg([
            'Type' => 6,
            'Content' => sprintf("<appmsg appid='wxeb7ec651dd0aefa9' sdkver=''><title>%s</title><des></des><action></action><type>6</type><content></content><url></url><lowurl></lowurl><appattach><totallen>%s</totallen><attachid>%s</attachid><fileext>%s</fileext></appattach><extinfo></extinfo></appmsg>", basename($file), filesize($file), $mediaId, $fileName),
            'FromUserName' => vbot('myself', $id)->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    protected function getExpand(): array
    {
        return ['title' => $this->title];
    }

    protected function afterCreate(int|string $id = 0)
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
    }

    protected static function getDownloadUrl($message)
    {
        $serverConfig = vbot('config')['server'];

        return $serverConfig['uri']['file'] . DIRECTORY_SEPARATOR . static::DOWNLOAD_API;
    }

    protected static function getDownloadOption($msg, int|string $id = 0)
    {
        return [
            'query' => [
                'sender' => $msg['FromUserName'],
                'mediaid' => $msg['MediaId'],
                'filename' => $msg['FileName'],
                'fromuser' => vbot('myself', $id)->username,
                'pass_ticket' => vbot('config', $id)['server.passTicket'],
                'webwx_data_ticket' => static::getTicket($id),
            ],
        ];
    }

    protected static function fileName($message)
    {
        return $message['FileName'];
    }

    protected function parseToContent(): string
    {
        return '[文件]' . $this->title;
    }
}
