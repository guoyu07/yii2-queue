<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use yii\base\Object;

/**
 * Class Job
 * @package xutl\queue
 */
abstract class Job extends Object
{
    /**
     * Runs the job.
     */
    abstract public function run();
}