<?php

declare(strict_types=1);

namespace Hanson\Vbot\Extension;

use Hanson\Vbot\Exceptions\ExtensionException;
use Hanson\Vbot\Message\Text;
use Hyperf\Collection\Collection;

abstract class AbstractMessageHandler
{
    public $version = '1.0';

    public $author = 'HanSon';

    public int|string $id = 0;

    public $name;

    public $zhName;

    public $status = true;

    public static $admin;

    public $baseExtensions = [];

    /**
     * 拓展配置.
     */
    public $config;

    /**
     * 初始化拓展.
     */
    public function init(int|string $id = 0)
    {
        $this->id = $id;

        $this->config = vbot('config', $id)->get('extension.' . $this->name);

        $this->admin();

        $this->register();

        return $this;
    }

    /**
     * 注册拓展时的操作.
     */
    abstract public function register();

    /**
     * 开发者需要实现的方法.
     *
     * @return mixed
     */
    abstract public function handler(Collection $collection);

    /**
     * 消息处理器.
     *
     * @return mixed
     */
    final public function messageHandler(Collection $collection)
    {
        if ($collection['type'] === 'text' && $this->isAdmin($collection['username'])) {
            if (str_starts_with($collection['content'], $this->name . ' ')) {
                $content = str_replace($this->name . ' ', '', $collection['content']);

                switch ($content) {
                    case 'info':
                        $this->applicationInfo($this->id, $collection);
                        break;
                    case 'on':
                        $this->setStatus(true, $collection);
                        break;
                    case 'off':
                        $this->setStatus(false, $collection);
                        break;
                    default:
                        break;
                }
            }
        }

        if (! $this->status) {
            return false;
        }

        return $this->handler($collection);
    }

    final public function applicationInfo(int|string $id, $collection)
    {
        $status = $this->status ? '开' : '关';

        $admin = static::$admin;

        Text::send($id, $collection['from']['UserName'], "当前应用名称：{$this->zhName}\n名称：{$this->name}\n状态：{$status}\n版本：{$this->version}\n作者：{$this->author}\n管理员 Username：{$admin}");
    }

    /**
     * 设置拓展开关.
     * @param mixed $collection
     */
    final public function setStatus(bool $boolean, $collection)
    {
        $this->status = $boolean;

        $status = $this->status ? '开' : '关';

        Text::send($this->id, $collection['from']['UserName'], "应用：{$this->zhName} 状态已更改为：{$status}");
    }

    /**
     * 设置管理员.
     *
     * @throws ExtensionException
     */
    final public function admin()
    {
        $remark = vbot('config', $this->id)->get('extension.admin.remark');

        if ($remark) {
            static::$admin = vbot('friends', $this->id)->getUsernameByRemarkName($remark);
        }

        if (! $remark && ($nickname = vbot('config', $this->id)->get('extension.admin.nickname'))) {
            static::$admin = vbot('friends', $this->id)->getUsernameByNickname($nickname);
        }
    }

    /**
     * 判断是否管理员.
     * @param mixed $username
     */
    final public function isAdmin($username): bool
    {
        return $username === static::$admin || $username === vbot('myself', $this->id)->username;
    }
}
