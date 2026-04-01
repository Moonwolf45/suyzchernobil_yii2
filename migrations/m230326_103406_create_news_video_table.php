<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%news_video}}`.
 */
class m230326_103406_create_news_video_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->createTable('{{%news_video}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'slug' => Schema::TYPE_STRING . '(255) NOT NULL UNIQUE',
            'meta_keywords' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'video' => Schema::TYPE_STRING . '(255) NULL DEFAULT NULL',
            'views' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 0',
            'twisted_views' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ]);

        $this->createIndex('{{%unique-news_video-slug}}', '{{%news_video}}', 'slug', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropIndex('{{%unique-news_video-slug}}', '{{%news_video}}');

        $this->dropTable('{{%news_video}}');
    }
}
