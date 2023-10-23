<?php

declare(strict_types=1);

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Core\ApiExceptionHandler;

class Friends extends Contacts
{
    /**
     * 根据微信号获取联系人.
     *
     * @param mixed $alias
     * @return mixed
     */
    public function getContactByAlias($alias)
    {
        return $this->getObject($alias, 'Alias', true);
    }

    /**
     * 根据微信号获取联系username.
     *
     * @param mixed $alias
     * @return mixed
     */
    public function getUsernameByAlias($alias)
    {
        return $this->getUsername($alias, 'Alias');
    }

    /**
     * 设置备注.
     *
     * @param mixed $username
     * @param mixed $remarkName
     * @return bool
     */
    public function setRemarkName($username, $remarkName)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', $this->vbot->config['server.uri.base'], $this->vbot->config['server.passTicket']);

        $result = $this->vbot->http->post($url, json_encode([
            'UserName' => $username,
            'CmdId' => 2,
            'RemarkName' => $remarkName,
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 设置是否置顶.
     *
     * @param bool $isStick
     * @param mixed $username
     *
     * @return bool
     */
    public function setStick($username, $isStick = true)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', $this->vbot->config['server.uri.base'], $this->vbot->config['server.passTicket']);

        $result = $this->vbot->http->json($url, [
            'UserName' => $username,
            'CmdId' => 3,
            'OP' => (int) $isStick,
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 主动添加好友.
     * @param mixed $username
     * @param null|mixed $content
     */
    public function add($username, $content = null)
    {
        $this->verifyUser(2, [
            'Value' => $username,
            'VerifyUserTicket' => '',
        ], $content);
    }

    /**
     * 通过好友申请.
     * @param mixed $message
     */
    public function approve($message)
    {
        $this->verifyUser(3, [
            'Value' => $message['info']['UserName'],
            'VerifyUserTicket' => $message['info']['Ticket'],
        ]);
    }

    /**
     * 验证通过好友.
     *
     * @param mixed $code
     * @param mixed $userList
     * @param null|mixed $content
     * @return bool
     */
    public function verifyUser($code, $userList, $content = null)
    {
        $url = sprintf($this->vbot->config['server.uri.base'] . '/webwxverifyuser?lang=zh_CN&r=%s&pass_ticket=%s', time() * 1000, $this->vbot->config['server.passTicket']);
        $data = [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'Opcode' => $code,
            'VerifyUserListSize' => 1,
            'VerifyUserList' => [$userList],
            'VerifyContent' => $content,
            'SceneListCount' => 1,
            'SceneList' => [33],
            'skey' => $this->vbot->config['server.skey'],
        ];

        $result = $this->vbot->http->post($url, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return ApiExceptionHandler::handle($result, id: $this->vbot->getId());
    }

    /**
     * 更新群组.
     * @param mixed $username
     * @param null|mixed $list
     */
    public function update($username, $list = null): array
    {
        $username = is_array($username) ?: [$username];

        return parent::update($username, $this->makeUsernameList($username));
    }

    /**
     * 生成username list 格式.
     *
     * @param mixed $username
     * @return array
     */
    public function makeUsernameList($username)
    {
        $usernameList = [];

        foreach ($username as $item) {
            $usernameList[] = ['UserName' => $item, 'EncryChatRoomId' => ''];
        }

        return $usernameList;
    }
}
