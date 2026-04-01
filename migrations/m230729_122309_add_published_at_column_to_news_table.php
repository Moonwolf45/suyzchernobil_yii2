<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%news}}`.
 */
class m230729_122309_add_published_at_column_to_news_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->addColumn('{{%news}}', 'published_at', Schema::TYPE_INTEGER . ' UNSIGNED NULL DEFAULT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%news}}', 'published_at');
    }
}
