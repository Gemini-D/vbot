<?php

declare(strict_types=1);

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Content;
use Hyperf\Collection\Collection;

class Contacts extends Collection
{
    protected ?Vbot $vbot = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function setVbot(Vbot $vbot)
    {
        $this->vbot = $vbot;

        return $this;
    }

    /**
     * 根据昵称获取对象
     *
     * @param bool $blur
     * @param mixed $nickname
     *
     * @return bool|string
     */
    public function getUsernameByNickname($nickname, $blur = false)
    {
        return $this->getUsername($nickname, 'NickName', $blur);
    }

    /**
     * 根据备注获取对象
     *
     * @param mixed $remark
     * @param mixed $blur
     * @return mixed
     */
    public function getUsernameByRemarkName($remark, $blur = false)
    {
        return $this->getUsername($remark, 'RemarkName', $blur);
    }

    /**
     * 获取Username.
     *
     * @param bool $blur
     * @param mixed $search
     * @param mixed $key
     *
     * @return string
     */
    public function getUsername($search, $key, $blur = false)
    {
        return $this->search(function ($item) use ($search, $key, $blur) {
            if (! isset($item[$key])) {
                return false;
            }

            if ($blur && str_contains($item[$key], $search)) {
                return true;
            }
            if (! $blur && $item[$key] === $search) {
                return true;
            }

            return false;
        });
    }

    /**
     * 获取整个数组.
     *
     * @param bool $blur
     * @param mixed $search
     * @param mixed $key
     *
     * @return mixed|static
     */
    public function getObject($search, $key, $blur = false)
    {
        $username = $this->getUsername($search, $key, $blur);

        return $username ? $this->get($username) : null;
    }

    /**
     * 根据username获取账号.
     *
     * @param mixed $username
     * @return mixed
     */
    public function getAccount($username)
    {
        if (str_starts_with($username, '@@')) {
            return $this->vbot->groups->get($username);
        }
        $account = $this->vbot->friends->get($username, null);

        $account = $account ?: $this->vbot->members->get($username, null);

        $account = $account ?: $this->vbot->officials->get($username, null);

        return $account ?: $this->vbot->specials->get($username, null);
    }

    public function getAvatar($username)
    {
        $params = [
            'userName' => $username,
            'type' => 'big',
        ];

        $api = $this->vbot->groups->isGroup($username) ? '/webwxgetheadimg' : '/webwxgeticon';

        return $this->vbot->http->get($this->vbot->config['server.uri.base'] . $api, ['query' => $params]);
    }

    /**
     * 存储时处理emoji.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Collection
     */
    public function put($key, $value): static
    {
        $value = $this->format($value);

        return parent::put($key, $value);
    }

    /**
     * 处理联系人.
     *
     * @param mixed $contact
     * @return mixed
     */
    public function format($contact)
    {
        if (isset($contact['DisplayName'])) {
            $contact['DisplayName'] = Content::emojiHandle($contact['DisplayName']);
        }

        if (isset($contact['RemarkName'])) {
            $contact['RemarkName'] = Content::emojiHandle($contact['RemarkName']);
        }

        if (isset($contact['Signature'])) {
            $contact['Signature'] = Content::emojiHandle($contact['Signature']);
        }

        $contact['NickName'] = Content::emojiHandle($contact['NickName']);

        return $contact;
    }

    /**
     * 通过接口更新群组信息.
     * @param mixed $username
     * @param mixed $list
     */
    public function update($username, $list): array
    {
        $usernames = is_string($username) ? [$username] : $username;

        $url = $this->vbot->config['server.uri.base'] . '/webwxbatchgetcontact?type=ex&r=' . time();

        $data = [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'Count' => count($usernames),
            'List' => $list,
        ];

        $response = $this->vbot->http->json($url, $data, true);

        if (! $response) {
            return [];
        }

        foreach ($response['ContactList'] as $item) {
            $this->put($item['UserName'], $item);
        }

        return is_string($username) ? head($response['ContactList']) : $response['ContactList'];
    }
}
