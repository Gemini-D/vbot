<?php

declare(strict_types=1);

namespace Hanson\Vbot\Message;

/**
 * Class Transfer.
 */
class Transfer extends Message implements MessageInterface
{
    public const TYPE = 'transfer';

    /**
     * 转账金额 单位 �
     * �.
     *
     * @var string
     */
    private $fee;

    /**
     * 交易流水号.
     */
    private $transactionId;

    /**
     * 转账说明.
     *
     * @var string
     */
    private $memo;

    private $content;

    public function make($msg, int|string $id = 0)
    {
        return $this->getCollection($msg, static::TYPE, $id);
    }

    protected function afterCreate(int|string $id = 0)
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $des = (array) $array['appmsg']->des;
        $fee = (array) $array['appmsg']->wcpayinfo;

        $this->content = current($des);

        $this->memo = is_string($fee['pay_memo']) ? $fee['pay_memo'] : null;
        $this->fee = substr($fee['feedesc'], 3);
        $this->transactionId = $fee['transcationid'];
    }

    protected function getExpand(): array
    {
        return ['fee' => $this->fee, 'transaction_id' => $this->transactionId, 'memo' => $this->memo];
    }

    protected function parseToContent(): string
    {
        return $this->content;
    }
}
