<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%documents}}`.
 */
class m260805_015251_add_type_column_to_documents_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%documents}}', 'type', Schema::TYPE_STRING . '(255) DEFAULT "book_of_memory" AFTER `title`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%documents}}', 'type');
    }
}
