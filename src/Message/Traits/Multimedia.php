<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message\Traits;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Core\ApiExceptionHandler;
use Hanson\Vbot\Exceptions\ArgumentException;
use Hanson\Vbot\Support\Common;
use Hanson\Vbot\Support\File;

trait Multimedia
{
    private static $file;

    /**
     * download multimedia.
     *
     * @param mixed $message
     * @param null|mixed $callback
     * @return bool
     * @throws ArgumentException
     */
    public static function download($message, $callback = null)
    {
        if (! $callback) {
            static::autoDownload($message['raw'], true);

            return true;
        }

        if ($callback && ! is_callable($callback)) {
            throw new ArgumentException();
        }

        call_user_func_array($callback, [static::getResource($message['raw'])]);

        return true;
    }

    /**
     * @param mixed $username
     * @param mixed $file
     * @return bool|mixed|string
     */
    public static function uploadVideo($username, $file, int|string $id = 0)
    {
        if (! is_file($file)) {
            return false;
        }

        $url = vbot('config', $id)['server.uri.file'] . '/webwxuploadmedia?f=json';

        static::$file = $file;
        [$mime, $mediaType] = static::getMediaType($file);

        $result = '';
        $fileSize = filesize($file);
        $streamLen = 524288;
        $chunks = ceil($fileSize / $streamLen);
        $chunk = 0;
        $clientMediaId = Common::getMillisecond();
        $fp = fopen($file, 'rb');
        while (! feof($fp)) {
            $data = [
                'id' => 'WU_FILE_0',
                'name' => basename($file),
                'type' => $mime,
                'lastModifiedDate' => gmdate('D M d Y H:i:s TO', filemtime($file)) . ' (CST)',
                'size' => $fileSize,
                'chunks' => $chunks,
                'chunk' => $chunk,
                'mediatype' => $mediaType,
                'uploadmediarequest' => json_encode([
                    'BaseRequest' => vbot('config', $id)['server.baseRequest'],
                    'ClientMediaId' => $clientMediaId,
                    'TotalLen' => $fileSize,
                    'StartPos' => 0,
                    'DataLen' => $fileSize,
                    'MediaType' => 4,
                    'UploadType' => 2,
                    'FromUserName' => vbot('myself', $id)->username,
                    'ToUserName' => $username,
                    'FileMd5' => md5_file($file),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'webwx_data_ticket' => static::getTicket($id),
                'pass_ticket' => vbot('config', $id)['server.passTicket'],
                'filename' => fread($fp, $streamLen),
            ];

            $data = static::dataToMultipart($data);

            $result = vbot('http', $id)->request($url, 'post', [
                'multipart' => $data,
            ]);
            $result = json_decode($result, true);

            ++$chunk;
        }
        fclose($fp);

        return ApiExceptionHandler::handle($result, id: $id);
    }

    /**
     * @param mixed $username
     * @param mixed $file
     * @return bool|mixed|string
     */
    public static function uploadMedia(int|string $id, $username, $file)
    {
        if (! is_file($file)) {
            return false;
        }

        $url = vbot('config', $id)['server.uri.file'] . '/webwxuploadmedia?f=json';

        static::$file = $file;
        [$mime, $mediaType] = static::getMediaType($file);

        $data = [
            'id' => 'WU_FILE_0',
            'name' => basename($file),
            'type' => $mime,
            'lastModifieDate' => gmdate('D M d Y H:i:s TO', filemtime($file)) . ' (CST)',
            'size' => filesize($file),
            'mediatype' => $mediaType,
            'uploadmediarequest' => json_encode([
                'BaseRequest' => vbot('config', $id)['server.baseRequest'],
                'ClientMediaId' => time(),
                'TotalLen' => filesize($file),
                'StartPos' => 0,
                'DataLen' => filesize($file),
                'MediaType' => 4,
                'UploadType' => 2,
                'FromUserName' => vbot('myself', $id)->username,
                'ToUserName' => $username,
                'FileMd5' => md5_file($file),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'webwx_data_ticket' => static::getTicket($id),
            'pass_ticket' => vbot('config', $id)['server.passTicket'],
            'filename' => fopen($file, 'r'),
        ];

        $data = static::dataToMultipart($data);

        $result = vbot('http', $id)->request($url, 'post', [
            'multipart' => $data,
        ]);
        $result = json_decode($result, true);

        return ApiExceptionHandler::handle($result, id: $id);
    }

    protected static function getDownloadUrl($message, int|string $id = 0)
    {
        $serverConfig = vbot('config', $id)['server'];

        return $serverConfig['uri']['base'] . DIRECTORY_SEPARATOR . static::DOWNLOAD_API . "{$message['MsgId']}&skey={$serverConfig['skey']}";
    }

    protected static function getDownloadOption($message, int|string $id = 0)
    {
        return [];
    }

    /**
     * download resource to a default path.
     *
     * @param bool $force
     * @param mixed $message
     */
    protected static function autoDownload($message, $force = false, int|string $id = 0)
    {
        $isDownload = vbot('config', $id)['download.' . static::TYPE];

        if ($isDownload || $force) {
            $resource = static::getResource($message, $id);

            if ($resource) {
                File::saveTo(vbot('config', $id)['user_path'] . static::TYPE . DIRECTORY_SEPARATOR .
                    static::fileName($message), $resource);
            }
        }
    }

    protected static function fileName($message)
    {
        return $message['MsgId'] . static::EXT;
    }

    protected static function getDefaultFile($message, int|string $id)
    {
        return vbot('config', $id)['user_path'] . static::TYPE . DIRECTORY_SEPARATOR . static::fileName($message);
    }

    /**
     * get a resource through api.
     *
     * @param mixed $message
     * @return mixed
     */
    private static function getResource($message, int|string $id = 0)
    {
        $url = static::getDownloadUrl($message, $id);

        $content = vbot('http', $id)->get($url, static::getDownloadOption($message, $id));

        if (! $content) {
            vbot('console', $id)->log('download file failed.', Console::WARNING);
        } else {
            return $content;
        }
    }

    /**
     * 获取媒体类型.
     *
     * @param mixed $file
     * @return array
     */
    private static function getMediaType($file)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $file);
        finfo_close($info);

        $fileExplode = explode('.', $file);
        $fileExtension = end($fileExplode);

        return [$mime, $fileExtension === 'jpg' ? 'pic' : ($fileExtension === 'mp4' ? 'video' : 'doc')];
    }

    /**
     * 获取cookie的ticket.
     *
     * @return mixed
     */
    private static function getTicket(int|string $id)
    {
        $cookies = vbot('http', $id)->getClient()->getConfig('cookies')->toArray();

        $key = array_search('webwx_data_ticket', array_column($cookies, 'Name'));

        return $cookies[$key]['Value'];
    }

    /**
     * 把请求数组转为multipart模式.
     *
     * @param mixed $data
     * @return array
     */
    private static function dataToMultipart($data)
    {
        $result = [];

        foreach ($data as $key => $item) {
            $field = [
                'name' => $key,
                'contents' => $item,
            ];
            if ($key === 'filename') {
                $field['filename'] = basename(static::$file);
            }
            $result[] = $field;
        }

        return $result;
    }
}
