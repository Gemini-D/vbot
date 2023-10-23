<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\SendAble;

class Card extends Message implements MessageInterface
{
    use SendAble;

    public const TYPE = 'card';

    public const API = 'webwxsendmsg?';

    /**
     * @var array 推荐信息
     */
    private $info;

    private $bigAvatar;

    private $smallAvatar;

    private $isOfficial = false;

    private $description;

    /**
     * 国�
     * 为省，国外为国.
     *
     * @var string
     */
    private $province;

    /**
     * 城市
     *
     * @var string
     */
    private $city;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    public static function send(int|string $id, $username, $alias, $nickname = null)
    {
        if (! $alias || ! $username) {
            return false;
        }

        return static::sendMsg([
            'Type' => 42,
            'Content' => "<msg username='{$alias}' nickname='{$nickname}'/>",
            'FromUserName' => vbot('myself', $id)->username,
            'ToUserName' => $username,
            'LocalID' => time() * 1e4,
            'ClientMsgId' => time() * 1e4,
        ], $id);
    }

    protected function getExpand(): array
    {
        return [
            'info' => $this->info, 'avatar' => $this->bigAvatar, 'small_avatar' => $this->smallAvatar,
            'province' => $this->province, 'city' => $this->city, 'description' => $this->description,
            'is_official' => $this->isOfficial,
        ];
    }

    protected function afterCreate(int|string $id = 0)
    {
        $this->info = $this->raw['RecommendInfo'];
        $isMatch = preg_match('/bigheadimgurl="(http:\/\/.+?)"\ssmallheadimgurl="(http:\/\/.+?)".+province="(.+?)"\scity="(.+?)".+certflag="(\d+)"\scertinfo="(.+?)"/', $this->message, $matches);

        if ($isMatch) {
            $this->bigAvatar = $matches[1];
            $this->smallAvatar = $matches[2];
            $this->province = $matches[3];
            $this->city = $matches[4];
            $flag = $matches[5];
            $desc = $matches[6];
            if (vbot('officials', $id)->isOfficial($flag)) {
                $this->isOfficial = true;
                $this->description = $desc;
            }
        }
    }

    protected function parseToContent(): string
    {
        return '[名片]';
    }
}
