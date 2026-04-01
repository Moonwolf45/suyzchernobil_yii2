<?php

use yii\db\Migration;

/**
 * Class m230219_112014_rename_email_column_to_users_table
 */
class m230219_112014_rename_email_column_to_users_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->renameColumn('{{%users}}', 'email', 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->renameColumn('{{%users}}', 'email', 'username');
    }
}
