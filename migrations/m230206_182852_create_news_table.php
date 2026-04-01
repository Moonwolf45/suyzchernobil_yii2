<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%news}}`.
 */
class m230206_182852_create_news_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%news}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'slug' => Schema::TYPE_STRING . '(255) NOT NULL UNIQUE',
            'meta_keywords' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'category_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'image' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'views' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 0',
            'twisted_views' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ]);

        $this->createIndex('{{%unique-news-slug}}', '{{%news}}', 'slug', true);
        $this->createIndex('{{%idx-news-category_id}}', '{{%news}}', 'category_id');

        $this->addForeignKey('{{%fk-news-category_id}}', '{{%news}}', 'category_id',
            '{{%categories}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('{{%fk-news-category_id}}', '{{%news}}');

        $this->dropIndex('{{idx-news-category_id}}', '{{%news}}');
        $this->dropIndex('{{%unique-news-slug}}', '{{%news}}');

        $this->dropTable('{{%news}}');
    }
}
