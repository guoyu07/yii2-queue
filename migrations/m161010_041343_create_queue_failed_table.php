<?php

use yii\db\Migration;

/**
 * Handles the creation for table `queue_failed`.
 */
class m161010_041343_create_queue_failed_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%queue_failed}}', [
            'id' => $this->primaryKey(),
            'queue' => $this->string(),
            'payload' => $this->text(),
            'failed_at' => $this->integer()->unsigned()
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%queue_failed}}');
    }
}
