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
     * The name of the default queue.
     *
     * @var string
     */
    protected $default = 'default';

    /**
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $expire = 60;

    private $transaction;

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
     * Get the queue or return the default.
     *
     * @param  string|null $queue
     * @return string
     */
    protected function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  string $queue
     * @return void
     */
    public function push($job, $queue = null)
    {
        return $this->pushToDatabase(0, $queue, $this->createPayload($job));
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  int $delay
     * @param  string $payload
     * @param  string $queue
     * @return void
     */
    public function later($delay, $job, $queue = null)
    {
        return $this->pushToDatabase($delay, $queue, $this->createPayload($job));
    }

    /**
     * @inheritdoc
     */
    public function pushToDatabase($delay, $queue, $payload, $attempts = 0)
    {
        $this->db->createCommand()->insert('{{%queue}}', [
            'queue' => $this->getQueue($queue),
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
        $queue = $this->getQueue($queue);

        if (!is_null($this->expire)) {
            $this->releaseJobsThatHaveBeenReservedTooLong($queue);
        }

        if ($job = $this->getNextAvailableJob($queue)) {
            $this->markJobAsReserved($job->id);
            $this->transaction->commit();
            return $job;
        }
        $this->transaction->commit();
    }

    /**
     * Get the next available job for the queue.
     *
     * @param  string|null $queue
     * @return \StdClass|null
     */
    protected function getNextAvailableJob($queue)
    {
        $this->transaction = $this->db->beginTransaction();
        $job = $this->db->createCommand('SELECT * FROM {{%queue}} WHERE queue=:queue AND reserved=:reserved AND available_at<=:available_at for update ')
            ->bindValue(':queue', $queue)
            ->bindValue(':reserved', 0)
            ->bindValue(':available_at', time())
            ->queryOne();
        return $job ? (object)$job : null;
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
        ], ['id' => $id])->execute();
    }

    /**
     * Release the jobs that have been reserved for too long.
     *
     * @param  string $queue
     * @return void
     */
    protected function releaseJobsThatHaveBeenReservedTooLong($queue)
    {
        return;
        $expired = time() + $this->expire;

        $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->where('reserved', 1)
            ->where('reserved_at', '<=', $expired)
            ->update([
                'reserved' => 0,
                'reserved_at' => null,
                'attempts' => new Expression('attempts + 1'),
            ]);
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
     * Release a reserved job back onto the queue.
     *
     * @param  string $queue
     * @param  \StdClass $job
     * @param  int $delay
     * @return void
     */
    public function release($queue, $job, $delay)
    {
        return $this->pushToDatabase($delay, $queue, $job['payload'], $job['attempts']);
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