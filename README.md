# yii2-queue
适用于Yii2的任务队列

###控制台配置
````php
    'controllerMap' => [
        'queue' => [
            'class' => 'xutl\queue\console\QueueController',
            
        ],
    ],
````    
    
###件配置
````php
        //使用Redis
        'queue' => [
            'class' => 'xutl\queue\RedisQueue',
            'redis' => [
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379,
                //'password' => '1984111a',
                'db' => 0
            ],
        ],
        
        //使用AWS SQS
        'queue' => [
            'class' => 'xutl\queue\SqsQueue',
            'sqs' => [
                //etc
            ],
        ],

        //DB模拟
        'queue' => [
            'class' => 'xutl\queue\DbQueue',
            'db' => 'db'
        ],
        
        //DB模拟2
        'queue' => [
            'class' => 'xutl\queue\DbQueue',
            'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=localhost;dbname=yuncms',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'tablePrefix' => 'yun_',
            ],
        ],
````