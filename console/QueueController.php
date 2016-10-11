<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\queue\console;

use Yii;
use yii\console\Controller;

/**
 * Queue
 */
class QueueController extends Controller
{
    /**
     * @var integer
     * Delay after each step (in seconds)
     */
    public $_sleep = 1;

    /**
     * @var integer
     * Delay before running first job in listening mode (in seconds)
     */
    public $_timeout;

    /**
     * @var bool
     * Need restart job if failure or not
     */
    public $restartOnFailure = true;

    /**
     * @var string
     * Queue component ID
     */
    public $queue = 'queue';

    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'listen'.
     */
    public $defaultAction = 'listen';

    /**
     * Purges the queue.
     * @param string $queue
     */
    public function actionPurge($queue)
    {
        $this->getQueue()->purge($queue);
        $this->stdout("Purges the queue. ok!...\r\n");
    }

    /**
     * Process a job
     *
     * @param string $queue
     * @throws \Exception
     */
    public function actionWork($queue)
    {
        $this->process($queue);
    }

    /**
     * Continuously process jobs
     *
     * @param string $queue
     * @return bool
     * @throws \Exception
     */
    public function actionListen($queue)
    {
        while (true) {
            if ($this->_timeout !== null) {
                if ($this->_timeout < time()) {
                    return true;
                }
            }
            if (!$this->process($queue)) {
                sleep($this->_sleep);
            }
        }
    }

    /**
     * Process one unit of job in queue
     *
     * @param string $queue
     * @return bool
     */
    protected function process($queue)
    {
        $message = $this->getQueue()->pop($queue);
        if ($message) {
            try {
                $this->stdout(sprintf('Begin executing a job `%s`...', get_class($job)));
                if ($job->run() || (bool)$this->restartOnFailure === false) {
                    $this->getQueue()->delete($message);
                }
                return true;
            } catch (\Exception $e) {
                $this->getQueue()->delete($message);
                $this->getQueue()->release($message);
                Yii::error($e->getMessage(), __METHOD__);
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (getenv('QUEUE_TIMEOUT')) {
            $this->_timeout = (int)getenv('QUEUE_TIMEOUT') + time();
        }
        if (getenv('QUEUE_SLEEP')) {
            $this->_sleep = (int)getenv('QUEUE_SLEEP');
        }
        return true;
    }

    /**
     * 获取队列
     * @return \xutl\queue\QueueInterface
     * @throws \yii\base\InvalidConfigException
     */
    private function getQueue()
    {
        return Yii::$app->get($this->queue);
    }
}