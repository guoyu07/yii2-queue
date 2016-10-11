<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue;

use Yii;
use yii\db\Connection;
use yii\db\Expression;
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
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $expire = 60;

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
            $this->db = Yii::createObject($this->db);
        }
        if (!$this->db instanceof Connection) {
            throw new InvalidConfigException("Queue::db must be application component ID of a SQL connection.");
        }
    }

    /**
     * 推送任务到队列
     *
     * @param mixed $payload
     * @param integer $delay
     * @param string $queue
     * @return string
     */
    public function push($payload, $queue, $delay = 0)
    {
        $this->db->createCommand()->insert('{{%queue}}', [
            'queue' => $queue,
            'attempts' => 0,
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
    public function pop($queue)
    {
        if (!is_null($this->expire)) {
            //将发布的消息重新放入队列
            $expired = time() + $this->expire;
            $this->db->createCommand("UPDATE {{%queue}} SET reserved=0, reserved_at=null, attempts=attempts+1 WHERE queue=:queue AND reserved=1 AND reserved_at<=:expired")
                ->bindValue(':queue', $queue)
                ->bindValue(':expired', $expired)
                ->execute();
        }
        //准备事务
        $transaction = $this->db->beginTransaction();
        if ($job = $this->receiveMessage($queue)) {
            $this->db->createCommand("UPDATE {{%queue}} SET reserved=1, reserved_at=:reserved_at WHERE id=:id")
                ->bindValue(':reserved_at', time())
                ->bindValue(':id', $job->id)
                ->execute();
            $transaction->commit();
            return $job;
        }
        $transaction->commit();
    }

    /**
     * Get the next available job for the queue.
     *
     * @param  string|null $queue
     * @return \StdClass|null
     */
    protected function receiveMessage($queue)
    {
        $job = $this->db->createCommand('SELECT * FROM {{%queue}} WHERE queue=:queue AND reserved=:reserved AND available_at<=:available_at for update ')
            ->bindValue(':queue', $queue)
            ->bindValue(':reserved', 0)
            ->bindValue(':available_at', time())
            ->queryOne();
        return $job ? (object)$job : null;
    }

    /**
     * 清空队列
     *
     * @param string $queue
     */
    public function purge($queue)
    {
        $this->db->delete('{{%queue}}', ['queue' => $queue])->execute();
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
}