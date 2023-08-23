<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;

class Voice extends Message implements MessageInterface
{
    use Multimedia;
    use SendAble;

    public const API = 'webwxsendappmsg?fun=async&f=json&';

    public const DOWNLOAD_API = 'webwxgetvoice?msgid=';

    public const EXT = '.mp3';

    public const TYPE = 'voice';

    public function make($msg)
    {
        static::autoDownload($msg);

        return $this->getCollection($msg, static::TYPE);
    }

    public static function send($username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw']);

        if (! is_file($file)) {
            return false;
        }

        $response = static::uploadMedia($username, $file);

        $explode = explode('.', $file);

        return static::sendMsg([
            'Type' => 6,
            'Content' => sprintf("<appmsg appid='wxeb7ec651dd0aefa9' sdkver=''><title>%s</title><des></des><action></action><type>6</type><content></content><url></url><lowurl></lowurl><appattach><totallen>%s</totallen><attachid>%s</attachid><fileext>%s</fileext></appattach><extinfo></extinfo></appmsg>", basename($file), filesize($file), $response['MediaId'], end($explode)),
            'FromUserName' => vbot('myself')->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ]);
    }

    protected function parseToContent(): string
    {
        return '[语音]';
    }
}
