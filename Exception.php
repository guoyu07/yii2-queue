<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;

/**
 * Class Exception
 * @package xutl\queue
 */
class Exception extends \yii\base\Exception
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('app', 'Queue Exception');
    }
}