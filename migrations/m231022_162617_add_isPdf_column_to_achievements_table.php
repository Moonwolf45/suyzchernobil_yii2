<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%achievements}}`.
 */
class m231022_162617_add_isPdf_column_to_achievements_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%achievements}}', 'isPdf', Schema::TYPE_TINYINT . ' UNSIGNED DEFAULT 0 AFTER `fasten`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%achievements}}', 'isPdf');
    }
}
