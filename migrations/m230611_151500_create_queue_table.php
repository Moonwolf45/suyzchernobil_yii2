<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%queue}}`.
 */
class m230611_151500_create_queue_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%queue}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'channel' => Schema::TYPE_STRING . '(255) NOT NULL',
            'job' => 'blob NOT NULL',
            'pushed_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'ttr' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'delay' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 0',
            'priority' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 1024',
            'reserved_at' => Schema::TYPE_INTEGER . ' UNSIGNED NULL DEFAULT NULL',
            'attempt' => Schema::TYPE_INTEGER . ' UNSIGNED NULL DEFAULT NULL',
            'done_at' => Schema::TYPE_INTEGER . ' UNSIGNED NULL DEFAULT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%queue}}');
    }
}
