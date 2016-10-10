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
     * Push a new job onto the queue.
     *
     * @param  string $payload
     * @param  string|null $queue
     * @param int $delay
     * @return mixed
     */
    public function push($payload, $queue = null, $delay = 0);

    /**
     * Pop the next job off of the queue.
     *
     * @param  string $queue
     * @return Job|null
     */
    public function pop($queue = null);
}