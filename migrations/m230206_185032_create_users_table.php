<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m230206_185032_create_users_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%users}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'email' => Schema::TYPE_STRING . '(255) NOT NULL UNIQUE',
            'password' => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'access_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL'
        ]);

        $this->createIndex('{{%unique-users-email}}', '{{%users}}', 'email', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropIndex('{{%unique-users-email}}', '{{%users}}');

        $this->dropTable('{{%users}}');
    }
}
