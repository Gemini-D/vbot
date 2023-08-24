<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core\Http;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Utils;
use Hanson\Vbot\Foundation\Vbot;
use RuntimeException;

use function is_array;
use function is_scalar;

class CacheCookieJar extends CookieJar
{
    /**
     * Create a new FileCookieJar object.
     *
     * @param bool $storeSessionCookies set to true to store session cookies
     *                                  in the cookie jar
     *
     * @throws RuntimeException if the file cannot be found or created
     */
    public function __construct(private string $key, private Vbot $vbot, private bool $storeSessionCookies = false)
    {
        parent::__construct();

        if ($this->vbot->cache->has($this->key)) {
            $this->load($this->key);
        }
    }

    /**
     * Saves the file when shutting down.
     */
    public function __destruct()
    {
        $this->save($this->key);
    }

    /**
     * Saves the cookies to a file.
     *
     * @throws RuntimeException if the file cannot be found or created
     */
    public function save(string $key): void
    {
        $json = [];
        /** @var SetCookie $cookie */
        foreach ($this as $cookie) {
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $json[] = $cookie->toArray();
            }
        }

        $jsonStr = Utils::jsonEncode($json);
        $this->vbot->cache->forever($key, $jsonStr);
    }

    /**
     * Load cookies from a JSON formatted file.
     *
     * Old cookies are kept unless overwritten by newly loaded ones.
     *
     * @throws RuntimeException if the file cannot be loaded
     */
    public function load(string $key): void
    {
        $json = $this->vbot->cache->get($key);
        if (! $json) {
            return;
        }

        $data = Utils::jsonDecode($json, true);
        if (is_array($data)) {
            foreach ($data as $cookie) {
                $this->setCookie(new SetCookie($cookie));
            }
        } elseif (is_scalar($data) && ! empty($data)) {
            throw new RuntimeException("Invalid cookie key: {$key}");
        }
    }
}
