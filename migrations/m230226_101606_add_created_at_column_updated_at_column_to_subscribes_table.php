<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%subscribes}}`.
 */
class m230226_101606_add_created_at_column_updated_at_column_to_subscribes_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->addColumn('{{%subscribes}}', 'created_at', Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL');
        $this->addColumn('{{%subscribes}}', 'updated_at', Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%subscribes}}', 'created_at');
        $this->dropColumn('{{%subscribes}}', 'updated_at');
    }
}
