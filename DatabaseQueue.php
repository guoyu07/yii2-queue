<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

/**
 * Class DatabaseQueue
 * @package xutl\queue
 */
class DatabaseQueue extends Queue
{
    /**
     * @var \yii\db\Connection|string Default database connection component name
     */
    public $connection = 'db';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_string($this->connection)) {
            $this->connection = Yii::$app->get($this->connection);
        } elseif (is_array($this->connection)) {
            if (!isset($this->connection['class'])) {
                $this->connection['class'] = Connection::className();
            }
            $this->connection = Yii::createObject($this->connection);
        }
        if (!$this->connection instanceof Connection) {
            throw new InvalidConfigException("Queue::connection must be application component ID of a SQL connection.");
        }
    }

    /**
     * 推送一个任务到队列
     * @param mixed $payload
     * @param null $queue
     * @param int $delay
     */
    public function push($payload, $queue = null, $delay = 0)
    {
        // TODO: Implement push() method.
    }

    /**
     * 弹出任务
     * @param string|null $queue
     */
    public function pop($queue = null)
    {

    }
}