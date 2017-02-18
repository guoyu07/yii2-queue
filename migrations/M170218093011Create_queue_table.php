<?php

namespace xutl\queue\migrations;

use yii\db\Migration;

class M170218093011Create_queue_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%queue}}', [
            'id' => $this->primaryKey(),
            'tag' => $this->string()->notNull()->comment('队列名称'),
            'reserved' => $this->boolean()->defaultValue(false)->comment('是否保留'),
            'attempts' => $this->integer(5)->defaultValue(0)->comment('尝试次数'),
            'payload' => $this->text()->notNull()->comment('载荷'),
            'reserved_at' => $this->integer()->unsigned()->comment('保留时间'),
            'available_at' => $this->integer()->unsigned()->comment('可以获取的时间'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%queue}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
