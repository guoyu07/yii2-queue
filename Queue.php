<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;
use yii\helpers\Json;
use yii\base\Component;

/**
 * Class Queue
 * @package xutl\queue
 */
abstract class Queue extends Component implements QueueInterface
{
    /**
     * @inheritdoc
     */
    public function push($payload, $queue, $delay = 0)
    {
        $payload = Json::encode(['id' => $id = md5(uniqid('', true)), 'body' => $payload]);

        if ($delay > 0) {
            $this->redis->zadd($queue . ':delayed', [$payload => time() + $delay]);
        } else {
            $this->redis->rpush($queue, [$payload]);
        }

        return $id;
    }

    /**
     * 序列化任务
     * @param $job
     * @return mixed
     */
    protected function encode($job)
    {
        return call_user_func('serializer', $job);
    }

    /**
     * 反序列化任务
     * @param $serialized
     * @return mixed
     */
    protected function decode($serialized)
    {
        return call_user_func('unserializer', $serialized);
    }
}