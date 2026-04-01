<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%news_image}}`.
 */
class m230206_184139_create_news_image_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%news_image}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'news_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'image' => Schema::TYPE_STRING . '(255) NOT NULL',
        ]);

        $this->createIndex('{{%idx-news_image-news_id}}', '{{%news_image}}', 'news_id');

        $this->addForeignKey('{{%fk-news_image-news_id}}', '{{%news_image}}', 'news_id',
            '{{%news}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('{{%fk-news_image-news_id}}', '{{%news_image}}');
        $this->dropIndex('{{%idx-news_image-news_id}}', '{{%news_image}}');

        $this->dropTable('{{%news_image}}');
    }
}
