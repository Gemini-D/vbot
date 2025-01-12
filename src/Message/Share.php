<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

class Share extends Message implements MessageInterface
{
    public const TYPE = 'share';

    private $title;

    private $description;

    private $url;

    private $app;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = strval($info['title']);
        $this->description = strval($info['des']);

        $appInfo = (array) $array['appinfo'];
        $this->app = strval($appInfo['appname']);

        $this->url = $this->raw['Url'];
    }

    protected function getExpand(): array
    {
        return ['title' => $this->title, 'description' => $this->description, 'app' => $this->app, 'url' => $this->url];
    }

    protected function parseToContent(): string
    {
        return '[分享]';
    }
}
