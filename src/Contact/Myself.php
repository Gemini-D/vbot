<?php

declare(strict_types=1);

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Content;
use Hyperf\Codec\Json;

class Myself
{
    public $nickname;

    public $username;

    public $uin;

    public $sex;

    public array $raw = [];

    protected ?Vbot $vbot = null;

    public function init($user)
    {
        $this->nickname = Content::emojiHandle($user['NickName']);
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];
        $this->raw = $user;

        $this->log();

        $this->setPath();
    }

    public function setVbot(Vbot $vbot)
    {
        $this->vbot = $vbot;

        return $this;
    }

    private function log()
    {
        $this->vbot->console->log('current user\'s nickname:' . $this->nickname);
        $this->vbot->console->log('current user\'s username:' . $this->username);
        $this->vbot->console->log('current user\'s uin:' . $this->uin);
        $this->vbot->console->log('current user\'s raw data:' . Json::encode($this->raw));
    }

    private function setPath()
    {
        $path = $this->vbot->config['user_path'];

        $this->vbot->config['user_path'] = $path . $this->uin . DIRECTORY_SEPARATOR;

        if (! is_dir($this->vbot->config['user_path']) && $this->uin) {
            mkdir($this->vbot->config['user_path'], 0755, true);
        }
    }
}
