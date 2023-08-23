<?php

declare(strict_types=1);

namespace Hanson\Vbot\Core;

use Carbon\Carbon;
use Hanson\Vbot\Exceptions\ArgumentException;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Text;
use Hyperf\Collection\Collection;

class MessageHandler
{
    /**
     * @var Vbot
     */
    protected $vbot;

    protected $handler;

    protected $customHandler;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function listen($server = null)
    {
        $this->vbot->beforeMessageObserver->trigger();

        $this->vbot->messageExtension->initServiceExtensions();

        $time = 0;

        while (true) {
            if ($this->customHandler) {
                call_user_func($this->customHandler);
            }

            $time = $this->heartbeat($time);

            if (! ($checkSync = $this->checkSync())) {
                continue;
            }

            if (! $this->handleCheckSync($checkSync[0], $checkSync[1])) {
                if ($server) {
                    $server->shutdown();
                } else {
                    break;
                }
            }
        }
    }

    /**
     * handle a sync from wechat.
     *
     * @param bool $test
     * @param mixed $retCode
     * @param mixed $selector
     *
     * @return bool
     */
    public function handleCheckSync($retCode, $selector, $test = false)
    {
        if (in_array($retCode, [1100, 1101, 1102, 1205])) { // 微信客户端上登出或者其他设备登录
            $this->vbot->console->log('vbot exit normally.');
            $this->vbot->cache->forget('session.' . $this->vbot->config['session']);

            return false;
        }
        if ($retCode != 0) {
            $this->vbot->needActivateObserver->trigger();
        } else {
            if (! $test) {
                $this->handleMessage($selector);
            }

            return true;
        }
    }

    /**
     * set a message handler.
     *
     * @param mixed $callback
     * @throws ArgumentException
     */
    public function setHandler($callback)
    {
        if (! is_callable($callback)) {
            throw new ArgumentException('Argument must be callable in ' . get_class());
        }

        $this->handler = $callback;
    }

    /**
     * set a custom handler.
     *
     * @param mixed $callback
     * @throws ArgumentException
     */
    public function setCustomHandler($callback)
    {
        if (! is_callable($callback)) {
            throw new ArgumentException('Argument must be callable in ' . get_class());
        }

        $this->customHandler = $callback;
    }

    /**
     * make a heartbeat every 30 minutes.
     *
     * @param mixed $time
     * @return int
     */
    private function heartbeat($time)
    {
        if (time() - $time > 1800) {
            Text::send('filehelper', 'heart beat ' . Carbon::now()->toDateTimeString());

            return time();
        }

        return $time;
    }

    private function checkSync()
    {
        return $this->vbot->sync->checkSync();
    }

    /**
     * 处理消息.
     * @param mixed $selector
     */
    private function handleMessage($selector)
    {
        if ($selector == 0) {
            return;
        }

        $message = $this->vbot->sync->sync();

        $this->log($message);

        $this->storeContactsFromMessage($message);

        if ($message['AddMsgList']) {
            foreach ($message['AddMsgList'] as $msg) {
                $collection = $this->vbot->messageFactory->make($msg);
                if ($collection) {
                    $this->console($collection);
                    if (! $this->vbot->messageExtension->exec($collection) && $this->handler) {
                        call_user_func_array($this->handler, [$collection]);
                    }
                }
            }
        }
    }

    /**
     * log the message.
     * @param mixed $message
     */
    private function log($message)
    {
        if ($this->vbot->messageLog && ($message['ModContactList'] || $message['AddMsgList'])) {
            $this->vbot->messageLog->info(json_encode($message));
        }
    }

    private function console(Collection $collection)
    {
        $this->vbot->console->message($collection['content']);
    }

    private function storeContactsFromMessage($message)
    {
        if (count($message['ModContactList']) > 0) {
            $this->vbot->contactFactory->store($message['ModContactList']);
        }
    }
}
