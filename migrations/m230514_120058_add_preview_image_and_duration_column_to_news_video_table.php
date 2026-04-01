<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles adding columns to table `{{%news_video}}`.
 */
class m230514_120058_add_preview_image_and_duration_column_to_news_video_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->addColumn('{{%news_video}}', 'preview_image', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL AFTER video');
        $this->addColumn('{{%news_video}}', 'duration', Schema::TYPE_STRING . '(255) NULL DEFAULT NULL AFTER preview_image');
        $this->addColumn('{{%news_video}}', 'duration', Schema::TYPE_TINYINT . '(255) NULL DEFAULT NULL AFTER preview_image');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%news_video}}', 'duration');
        $this->dropColumn('{{%news_video}}', 'preview_image');
    }
}
