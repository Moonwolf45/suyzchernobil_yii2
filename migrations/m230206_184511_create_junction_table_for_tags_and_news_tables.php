<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%tags_news}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%tags}}`
 * - `{{%news}}`
 */
class m230206_184511_create_junction_table_for_tags_and_news_tables extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%tags_news}}', [
            'tags_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'news_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'PRIMARY KEY(tags_id, news_id)',
        ]);

        $this->createIndex('{{%idx-tags_news-tags_id}}', '{{%tags_news}}', 'tags_id');
        $this->createIndex('{{%idx-tags_news-news_id}}', '{{%tags_news}}', 'news_id');

        $this->addForeignKey('{{%fk-tags_news-tags_id}}', '{{%tags_news}}', 'tags_id',
            '{{%tags}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk-tags_news-news_id}}', '{{%tags_news}}', 'news_id',
            '{{%news}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('{{%fk-tags_news-news_id}}', '{{%tags_news}}');
        $this->dropForeignKey('{{%fk-tags_news-tags_id}}', '{{%tags_news}}');

        $this->dropIndex('{{%idx-tags_news-news_id}}', '{{%tags_news}}');
        $this->dropIndex('{{%idx-tags_news-tags_id}}', '{{%tags_news}}');

        $this->dropTable('{{%tags_news}}');
    }
}
