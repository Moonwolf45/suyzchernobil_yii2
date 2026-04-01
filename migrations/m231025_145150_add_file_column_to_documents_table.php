<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%documents}}`.
 */
class m231025_145150_add_file_column_to_documents_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%documents}}', 'file', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL AFTER `image`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%documents}}', 'file');
    }
}
