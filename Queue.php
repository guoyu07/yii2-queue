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
     * 获取队列实例
     * @return QueueInterface
     */
    public static function getQueue()
    {
        return Yii::$app->get('queue');
    }
}