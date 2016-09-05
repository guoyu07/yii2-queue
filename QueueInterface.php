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
     * 推送载荷到队列
     *
     * @param mixed $payload 队列载荷
     * @param string $queue 队列名称
     * @param integer $delay 延迟
     * @return string
     */
    public function push($payload, $queue, $delay = 0);

    /**
     * 从队列弹出消息
     *
     * @param string $queue 队列名称
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
     * 释放队列
     *
     * @param array $message
     * @param integer $delay
     */
    public function release(array $message, $delay = 0);

    /**
     * 删除队列
     *
     * @param array $message
     */
    public function delete(array $message);
}