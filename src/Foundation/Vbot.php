<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation;

use Hanson\Vbot\Api\ApiHandler;
use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Console\QrCode;
use Hanson\Vbot\Contact\Contacts;
use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;
use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Contact\Officials;
use Hanson\Vbot\Contact\Specials;
use Hanson\Vbot\Core\Cache\SimpleCache;
use Hanson\Vbot\Core\Config\Repository;
use Hanson\Vbot\Core\ContactFactory;
use Hanson\Vbot\Core\MessageFactory;
use Hanson\Vbot\Core\MessageHandler;
use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Core\ShareFactory;
use Hanson\Vbot\Core\Swoole;
use Hanson\Vbot\Core\Sync;
use Hanson\Vbot\Extension\MessageExtension;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Observers\BeforeMessageObserver;
use Hanson\Vbot\Observers\ExitObserver;
use Hanson\Vbot\Observers\FetchContactObserver;
use Hanson\Vbot\Observers\LoginSuccessObserver;
use Hanson\Vbot\Observers\NeedActivateObserver;
use Hanson\Vbot\Observers\Observer;
use Hanson\Vbot\Observers\QrCodeObserver;
use Hanson\Vbot\Observers\ReLoginSuccessObserver;
use Hanson\Vbot\Observers\ScanQrCodeObserver;
use Hanson\Vbot\Support\Http;
use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Class Vbot.ShareFactory.
 *
 * @property Server $server
 * @property Swoole $swoole
 * @property MessageHandler $messageHandler
 * @property MessageFactory $messageFactory
 * @property ShareFactory $shareFactory
 * @property MessageExtension $messageExtension
 * @property Text $text
 * @property Sync $sync
 * @property ContactFactory $contactFactory
 * @property ExceptionHandler $exception
 * @property LoggerInterface $log
 * @property LoggerInterface $messageLog
 * @property Http $http
 * @property ApiHandler $api
 * @property QrCode $qrCode
 * @property Console $console
 * @property Observer $observer
 * @property QrCodeObserver $qrCodeObserver
 * @property ScanQrCodeObserver $scanQrCodeObserver
 * @property NeedActivateObserver $needActivateObserver
 * @property LoginSuccessObserver $loginSuccessObserver
 * @property ReLoginSuccessObserver $reLoginSuccessObserver
 * @property ExitObserver $exitObserver
 * @property FetchContactObserver $fetchContactObserver
 * @property BeforeMessageObserver $beforeMessageObserver
 * @property Repository $config
 * @property SimpleCache $cache
 * @property Myself $myself
 * @property Friends $friends
 * @property Contacts $contacts
 * @property Groups $groups
 * @property Members $members
 * @property Officials $officials
 * @property Specials $specials
 */
class Vbot extends Container
{
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

    public function __construct(array $config, protected int|string $id = 0)
    {
        $this->initializeConfig($config, $id);

        (new Kernel($this))->bootstrap();

        VbotFactory::set($id, $this);
    }

    public function __get(string $name)
    {
        return $this[$name];
    }

    public function getId(): int|string
    {
        return $this->id;
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

    private function initializeConfig(array $config, int|string $id = 0)
    {
        $path = defined('BASE_PATH') ? BASE_PATH . '/runtime/vbot' : __DIR__;
        $path = rtrim($path, '/') . '/' . $id;
        $default = [
            'path' => $path,
            'session' => 'vbot:' . $id,
            'download' => [
                'emoticon_path' => $path . '/emoticon',
            ],
        ];
        $this['config'] = new Repository(array_merge($default, $config));
    }
}
