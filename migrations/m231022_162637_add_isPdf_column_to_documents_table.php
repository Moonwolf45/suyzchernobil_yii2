<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%documents}}`.
 */
class m231022_162637_add_isPdf_column_to_documents_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%documents}}', 'isPdf', Schema::TYPE_TINYINT . ' UNSIGNED DEFAULT 0 AFTER `fasten`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%documents}}', 'isPdf');
    }
}
