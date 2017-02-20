<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\queue;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Exception\MnsException;

class MnsQueue extends Queue
{
    /**
     * @var  string
     */
    public $endPoint;

    /**
     * @var string
     */
    public $accessId;

    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var null|string
     */
    public $securityToken = null;

    /**
     * @var null|Config
     */
    public $config = null;

    /**
     * @var Client
     */
    private $mns;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty ($this->endPoint)) {
            throw new InvalidConfigException ('The "endPoint" property must be set.');
        }
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
        }
        if (empty ($this->accessKey)) {
            throw new InvalidConfigException ('The "accessKey" property must be set.');
        }

        if (!$this->mns instanceof Client) {
            $this->mns = new Client($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken, $this->config);
        }
    }

    /**
     * @inheritdoc
     */
    public function push($payload, $queue, $delay = 0)
    {
        $queue = $this->mns->getQueueRef($queue);
        $request = new SendMessageRequest(Json::encode($payload), $delay);
        try {
            $response = $queue->sendMessage($request);
            return $response->getMessageId();
        } catch (MnsException $e) {
            Yii::trace(sprintf('Send Message Failed:  `%s`...', $e));
        }
    }

    /**
     * @inheritdoc
     */
    public function pop($queue)
    {
        $queuea = $this->mns->getQueueRef($queue);
        try {
            $response = $queuea->receiveMessage(30);
            return [
                'id' => $response->getMessageId(),
                'body' => $response->getMessageBody(),
                'queue' => $queue,
                'receipt-handle' =>$response->getReceiptHandle(),
            ];
        } catch (MnsException $e) {
            Yii::trace(sprintf('Receive Message Failed:  `%s`...', $e));
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(array $message)
    {
        $queue = $this->mns->getQueueRef($message['queue']);
        try {
            $queue->deleteMessage($message['receipt-handle']);
            return true;
        } catch (MnsException $e) {
            Yii::trace(sprintf('Delete Message Failed:  `%s`...', $e));
        }
    }
}