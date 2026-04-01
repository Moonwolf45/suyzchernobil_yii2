<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%categories}}`.
 */
class m230206_182827_create_categories_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%categories}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'slug' => Schema::TYPE_STRING . '(255) NOT NULL UNIQUE',
            'meta_keywords' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'main_status' => Schema::TYPE_TINYINT . '(1) UNSIGNED DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ]);

        $this->createIndex('{{%unique-categories-slug}}', '{{%categories}}', 'slug', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropIndex('{{%unique-categories-slug}}', '{{%categories}}');

        $this->dropTable('{{%categories}}');
    }
}
