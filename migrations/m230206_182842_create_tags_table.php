<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%tags}}`.
 */
class m230206_182842_create_tags_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%tags}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'slug' => Schema::TYPE_STRING . '(255) NOT NULL UNIQUE',
            'meta_keywords' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ]);

        $this->createIndex('{{%unique-tags-slug}}', '{{%tags}}', 'slug', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropIndex('{{%unique-tags-slug}}', '{{%tags}}');

        $this->dropTable('{{%tags}}');
    }
}
