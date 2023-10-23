<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hyperf\Collection\Arr;

class Official extends Message implements MessageInterface
{
    public const TYPE = 'official';

    private $title;

    private $description;

    private $url;

    private $articles;

    private $app;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
        $this->description = (string) $info['des'];
        $this->articles = $this->getArticles($info);

        $appInfo = (array) $array['appinfo'];

        $this->app = $appInfo['appname'];

        $this->url = $this->raw['Url'];
    }

    protected function getExpand(): array
    {
        return ['title' => $this->title, 'description' => $this->description, 'app' => $this->app, 'url' => $this->url,
            'articles' => $this->articles, ];
    }

    protected function parseToContent(): string
    {
        return '[公众号消息]';
    }

    private function getArticles($info)
    {
        if ($m = (array) Arr::get($info, 'mmreader') and isset($m['category'])) {
            $articles = [];

            foreach ($m['category'] as $key => $article) {
                if ($key === 'item') {
                    $articles[] = [
                        'title' => (string) Arr::get((array) $article, 'title'),
                        'cover' => (string) Arr::get((array) $article, 'cover'),
                        'url' => (string) Arr::get((array) $article, 'url'),
                    ];
                }
            }

            return $articles;
        }

        return [];
    }
}
