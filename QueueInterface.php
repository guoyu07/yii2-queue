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
     * Pushs payload to the queue.
     *
     * @param mixed $payload
     * @param integer $delay
     * @param string $queue
     * @return string
     */
    public function push($payload, $queue, $delay = 0);

    /**
     * Pops message from the queue.
     *
     * @param string $queue
     * @return array|false
     */
    public function pop($queue);

    /**
     * Purges the queue.
     *
     * @param string $queue
     */
    public function purge($queue);

    /**
     * Releases the message.
     *
     * @param array $message
     * @param integer $delay
     */
    public function release(array $message, $delay = 0);

    /**
     * Deletes the message.
     *
     * @param array $message
     */
    public function delete(array $message);
}