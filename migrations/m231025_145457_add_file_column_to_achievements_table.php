<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%achievements}}`.
 */
class m231025_145457_add_file_column_to_achievements_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%achievements}}', 'file', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL AFTER `image`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%achievements}}', 'file');
    }
}
