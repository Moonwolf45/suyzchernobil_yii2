<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%news}}`.
 */
class m230730_172533_add_published_at_ok_column_to_news_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->renameColumn('{{%news}}', 'published_at', 'published_at_vk');

        $this->addColumn('{{%news}}', 'published_at_ok', Schema::TYPE_INTEGER . ' UNSIGNED NULL DEFAULT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%news}}', 'published_at_ok');
    }
}
