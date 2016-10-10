<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

class DatabaseQueue extends Queue
{
    public function init()
    {
        parent::init();
    }

    /**
     * 弹出任务
     * @param string|null $queue
     */
    public function pop($queue = null)
    {

    }
}