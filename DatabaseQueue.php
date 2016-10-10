<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;
use yii\helpers\Json;
use yii\db\Connection;
use yii\base\InvalidConfigException;

/**
 * Class DatabaseQueue
 * @package xutl\queue
 */
class DatabaseQueue extends Queue
{
    /**
     * @var \yii\db\Connection|string Default database connection component name
     */
    public $db = 'db';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_string($this->db)) {
            $this->db = Yii::$app->get($this->db);
        } elseif (is_array($this->db)) {
            if (!isset($this->db['class'])) {
                $this->db['class'] = Connection::className();
            }
            $this->db = Yii::createObject($this->connection);
        }
        if (!$this->db instanceof Connection) {
            throw new InvalidConfigException("Queue::db must be application component ID of a SQL connection.");
        }
    }

    /**
     * @inheritdoc
     */
    public function push($payload, $queue = null, $delay = 0, $attempts = 0)
    {
        $this->db->createCommand()->insert('{{%queue}}', [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved' => false,
            'reserved_at' => null,
            'payload' => $payload,
            'available_at' => time() + $delay,
            'created_at' => time(),
        ])->execute();
        return $this->db->lastInsertID;
    }

    /**
     * 从队列弹出消息
     *
     * @param string|null $queue 队列名称
     * @return array|false
     */
    public function pop($queue = null)
    {
        $transaction = $this->db->beginTransaction();
        if ($queue) {
            $job = $this->db->createCommand('SELECT * FROM {{%queue}} WHERE queue=:queue AND reserved=:reserved AND available_at<=:available_at for update ')
                ->bindValue(':queue', $queue)
                ->bindValue(':reserved', 0)
                ->bindValue(':available_at', time())
                ->queryOne();
        } else {
            $job = $this->db->createCommand('SELECT * FROM {{%queue}} WHERE reserved=:reserved AND available_at<=:available_at for update ')
                ->bindValue(':reserved', 0)
                ->bindValue(':available_at', time())
                ->queryOne();
        }
        if ($job) {
            $this->db->createCommand()->update('{{%queue}}', [
                'reserved' => true, 'reserved_at' => time(),
            ], ['id' => $job['id']])->execute();
            $transaction->commit();
            return $job;
        }
        $transaction->commit();
    }

    /**
     * 清空队列
     *
     * @param string $queue
     */
    public function purge($queue)
    {
        $this->db->delete('{{%queue}}', ['queue'=>$queue])->execute();
    }

    /**
     * Release a reserved job back onto the queue.
     *
     * @param array $message
     * @param integer $delay
     */
    public function release(array $message, $delay = 0)
    {

    }

    /**
     * 删除队列消息
     *
     * @param array $message
     */
    public function delete(array $message)
    {
        $this->db->createCommand()->delete('{{%queue}}', ['id' => $message['id']])->execute();
    }

    /**
     * Mark the given job ID as reserved.
     *
     * @param  string $id
     * @return void
     */
    protected function markJobAsReserved($id)
    {
        $this->db->createCommand()->update('{{%queue}}', [
            'reserved' => true, 'reserved_at' => time(),
        ], ['id' => $id]);
    }
}