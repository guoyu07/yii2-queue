<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

/**
 * QueueInterface
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
interface QueueInterface
{
    /**
     * 推送任务到队列
     *
     * @param mixed $payload
     * @param integer $delay
     * @param string $queue
     * @return string
     */
    public function push($payload, $queue, $delay = 0);

    /**
     * 从队列弹出任务
     *
     * @param string $queue
     * @return array|false
     */
    public function pop($queue);

    /**
     * 清空队列
     *
     * @param string $queue
     */
    public function purge($queue);

    /**
     * 发布消息
     *
     * @param array $message
     * @param integer $delay
     */
    public function release(array $message, $delay = 0);

    /**
     * 删除消息
     *
     * @param array $message
     */
    public function delete(array $message);
}