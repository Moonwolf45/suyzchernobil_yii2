<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%tags_news}}".
 *
 * @property int $tags_id
 * @property int $news_id
 *
 * @property News $news
 * @property Tag $tags
 */
class TagsNews extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%tags_news}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['tags_id', 'news_id'], 'required'],
            [['tags_id', 'news_id'], 'integer'],
            [['tags_id', 'news_id'], 'unique', 'targetAttribute' => ['tags_id', 'news_id']],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::class,
                'targetAttribute' => ['news_id' => 'id']],
            [['tags_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class,
                'targetAttribute' => ['tags_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'tags_id' => 'Tags ID',
            'news_id' => 'News ID',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return ActiveQuery
     */
    public function getNews(): ActiveQuery {
        return $this->hasOne(News::class, ['id' => 'news_id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return ActiveQuery
     */
    public function getTags(): ActiveQuery {
        return $this->hasOne(Tag::class, ['id' => 'tags_id']);
    }
}
