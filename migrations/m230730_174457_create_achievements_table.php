<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%achievements}}`.
 */
class m230730_174457_create_achievements_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%achievements}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'image' => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%achievements}}');
    }
}
