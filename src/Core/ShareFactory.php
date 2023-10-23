<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core;

use Exception;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\File;
use Hanson\Vbot\Message\Mina;
use Hanson\Vbot\Message\Official;
use Hanson\Vbot\Message\Share;
use Hanson\Vbot\Support\Content;

class ShareFactory
{
    public $type;

    public function __construct(protected Vbot $vbot)
    {
    }

    public function make($msg)
    {
        try {
            $xml = Content::formatContent($msg['Content']);

            $this->parse($xml);

            if ($this->type == 6) {
                return (new File())->make($msg);
            }
            if ($this->vbot->officials->get($msg['FromUserName'])) {
                return (new Official())->make($msg);
            }
            if ($this->type == 33) {
                return (new Mina())->make($msg);
            }
            return (new Share())->make($msg);
        } catch (Exception $e) {
            return;
        }
    }

    private function parse($xml)
    {
        if (str_starts_with($xml, '@')) {
            $xml = preg_replace('/(@\S+:\\n)/', '', $xml);
        }

        $array = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->type = $info['type'];
    }
}
