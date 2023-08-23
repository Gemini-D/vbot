<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation;

use Hanson\Vbot\Core\Config\Repository;
use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Class Vbot.ShareFactory.
 *
 * @property \Hanson\Vbot\Core\Server $server
 * @property \Hanson\Vbot\Core\Swoole $swoole
 * @property \Hanson\Vbot\Core\MessageHandler $messageHandler
 * @property \Hanson\Vbot\Core\MessageFactory $messageFactory
 * @property \Hanson\Vbot\Core\ShareFactory $shareFactory
 * @property \Hanson\Vbot\Extension\MessageExtension $messageExtension
 * @property \Hanson\Vbot\Message\Text $text
 * @property \Hanson\Vbot\Core\Sync $sync
 * @property \Hanson\Vbot\Core\ContactFactory $contactFactory
 * @property \Hanson\Vbot\Foundation\ExceptionHandler $exception
 * @property LoggerInterface $log
 * @property LoggerInterface $messageLog
 * @property \Hanson\Vbot\Support\Http $http
 * @property \Hanson\Vbot\Api\ApiHandler $api
 * @property \Hanson\Vbot\Console\QrCode $qrCode
 * @property \Hanson\Vbot\Console\Console $console
 * @property \Hanson\Vbot\Observers\Observer $observer
 * @property \Hanson\Vbot\Observers\QrCodeObserver $qrCodeObserver
 * @property \Hanson\Vbot\Observers\NeedActivateObserver $needActivateObserver
 * @property \Hanson\Vbot\Observers\LoginSuccessObserver $loginSuccessObserver
 * @property \Hanson\Vbot\Observers\ReLoginSuccessObserver $reLoginSuccessObserver
 * @property \Hanson\Vbot\Observers\ExitObserver $exitObserver
 * @property \Hanson\Vbot\Observers\FetchContactObserver $fetchContactObserver
 * @property \Hanson\Vbot\Observers\BeforeMessageObserver $beforeMessageObserver
 * @property \Hanson\Vbot\Core\Config\Repository $config
 * @property \Illuminate\Cache\Repository $cache
 * @property \Hanson\Vbot\Contact\Myself $myself
 * @property \Hanson\Vbot\Contact\Friends $friends
 * @property \Hanson\Vbot\Contact\Contacts $contacts
 * @property \Hanson\Vbot\Contact\Groups $groups
 * @property \Hanson\Vbot\Contact\Members $members
 * @property \Hanson\Vbot\Contact\Officials $officials
 * @property \Hanson\Vbot\Contact\Specials $specials
 */
class Vbot extends Container
{
    public static $instance;

    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\LogServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\ExceptionServiceProvider::class,
        ServiceProviders\CacheServiceProvider::class,
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\ObserverServiceProvider::class,
        ServiceProviders\ConsoleServiceProvider::class,
        ServiceProviders\MessageServiceProvider::class,
        ServiceProviders\ContactServiceProvider::class,
        ServiceProviders\ApiServiceProvider::class,
        ServiceProviders\ExtensionServiceProvider::class,
    ];

    public function __construct(array $config)
    {
        $this->initializeConfig($config);

        (new Kernel($this))->bootstrap();

        static::$instance = $this;
    }

    public function __get(string $name)
    {
        return $this[$name];
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Register providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    private function initializeConfig(array $config)
    {
        $path = defined('BASE_PATH') ? BASE_PATH . '/runtime/vbot' : __DIR__;
        $default = [
            'path' => $path,
            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'system' => $path . 'log', // 系统报错日志
                'message' => $path . 'log', // 消息日志
            ],
            'download' => [
                'emoticon_path' => $path . '/emoticon',
            ],
        ];
        $this['config'] = new Repository(array_merge($default, $config));
    }
}
