<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message\Traits;

use Hanson\Vbot\Core\ApiExceptionHandler;
use Hanson\Vbot\Message\Text;

/**
 * Trait SendAble.
 */
trait SendAble
{
    protected static function sendMsg($msg, int|string $id = 0)
    {
        $data = [
            'BaseRequest' => vbot('config', $id)['server.baseRequest'],
            'Msg' => $msg,
            'Scene' => 0,
        ];

        $result = vbot('http', $id)->post(
            static::getUrl(),
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            true
        );

        static::stopSync($id);

        sleep(1);

        return ApiExceptionHandler::handle($result, id: $id);
    }

    private static function getUrl(int|string $id = 0)
    {
        return vbot('config', $id)['server.uri.base'] . '/' . static::API . 'pass_ticket=' . vbot('config', $id)['server.passTicket'];
    }

    private static function stopSync(int|string $id = 0)
    {
        if (get_class(new static()) != Text::class) {
            Text::send($id, 'filehelper', 'stop sync');
        }
    }
}
