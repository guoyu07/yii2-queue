<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use yii\base\Component;

abstract class Queue extends Component implements QueueInterface
{
    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @return string
     */
    protected function createPayload($job)
    {
        return serialize($job);
    }
}