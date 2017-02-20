<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;
use yii\base\Component;

abstract class Queue extends Component
{
    /**
     * @param $payload
     * @param int $delay
     */
    public function push($payload, $delay = 0)
    {
        return $this->sendMessage($payload, $delay);
    }

    /**
     * 从队列弹出消息
     */
    public function pop()
    {
        $data = reset($response['Messages']);

        return [
            'id' => $data['MessageId'],
            'body' => $data['Body'],
            'queue' => $queue,
            'receipt-handle' => $data['receiptHandle'],
        ];

        $message = $this->receiveMessage();
    }

    public function delete(){

    }

    /**
     * @inheritdoc
     */
    public function release(array $message, $delay = 0)
    {

    }

    abstract public function changeMessageVisibility($receiptHandle, $visibilityTimeout);

    abstract public function receiveMessage();

    abstract public function deleteMessage($receiptHandle);

    abstract public function sendMessage($payload, $delay);
}