<?php

declare(strict_types=1);

namespace Hanson\Vbot\Support;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\FileCookieJar;
use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Foundation\Vbot;

class Http
{
    public static $instance;

    protected $client;

    /**
     * @var FileCookieJar;
     */
    protected $cookieJar;

    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
        $this->cookieJar = new FileCookieJar($vbot->config['cookie_file'], true);
        $this->client = new HttpClient(['cookies' => $this->cookieJar]);
    }

    public function get($url, array $options = [])
    {
        return $this->request($url, 'GET', $options);
    }

    public function post($url, $query = [], $array = false)
    {
        $key = is_array($query) ? 'form_params' : 'body';

        $content = $this->request($url, 'POST', [$key => $query]);

        return $array ? json_decode($content, true) : $content;
    }

    public function json($url, $params = [], $array = false, $extra = [])
    {
        $params = array_merge(['json' => $params], $extra);

        $content = $this->request($url, 'POST', $params);

        return $array ? json_decode($content, true) : $content;
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $method
     * @param array $options
     * @param bool $retry
     * @param mixed $url
     *
     * @return string
     */
    public function request($url, $method = 'GET', $options = [], $retry = false)
    {
        try {
            $options = array_merge(['timeout' => 10, 'verify' => false], $options);

            $response = $this->getClient()->request($method, $url, $options);

            $this->cookieJar->save($this->vbot->config['cookie_file']);

            // if(str_contains($url, 'synccheck')){
            //     var_dump($response, (string) $response->getBody());
            // }

            return (string) $response->getBody();
        } catch (Exception $e) {
            $this->vbot->console->log($url . $e->getMessage(), Console::ERROR, true);

            if (! $retry) {
                return $this->request($url, $method, $options, true);
            }

            return false;
        }
    }
}
