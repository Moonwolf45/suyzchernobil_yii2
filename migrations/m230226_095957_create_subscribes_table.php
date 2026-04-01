<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%subscribes}}`.
 */
class m230226_095957_create_subscribes_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%subscribes}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'email' => Schema::TYPE_STRING . '(255) NOT NULL',
            'status' => Schema::TYPE_TINYINT . '(1) DEFAULT 1'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%subscribes}}');
    }
}
