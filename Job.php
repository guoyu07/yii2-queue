<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;
use yii\base\Object;

/**
 * Class Job
 * @package xutl\queue
 */
abstract class Job extends Object
{
    /**
     * 执行任务
     */
    abstract public function run();

    /**
     * 获取任务名称
     * @return string
     */
    abstract public function jobName();


    /**
     * @return QueueInterface
     */
    public static function getQueue()
    {
        return Yii::$app->get('queue');
    }

    /**
     * 推送当前任务到队列
     *
     * @param integer $delay
     * @return string
     */
    public function push($delay = 0)
    {
        return $this->getQueue()->push($this, $this->jobName(),$delay);
    }
}