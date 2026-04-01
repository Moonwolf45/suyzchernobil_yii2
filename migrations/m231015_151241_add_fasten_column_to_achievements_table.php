<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%achievements}}`.
 */
class m231015_151241_add_fasten_column_to_achievements_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%achievements}}', 'fasten', Schema::TYPE_TINYINT . ' UNSIGNED DEFAULT 0 AFTER `image`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%achievements}}', 'fasten');
    }
}
