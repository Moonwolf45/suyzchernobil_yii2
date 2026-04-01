<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%news_image}}".
 *
 * @property int $id
 * @property int $news_id
 * @property string $image
 *
 * @property News $news
 */
class NewsImage extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%news_image}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array {
        return [
            [
                'class' => CacheBehavior::class,
                'cacheName' => 'News'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['news_id', 'image'], 'required'],
            [['news_id'], 'integer'],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::class,
                'targetAttribute' => ['news_id' => 'id']],

            [['image'], 'file', 'extensions' => Yii::$app->params['extensionsImage'], 'maxSize' => 1024 * 1024 * 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'news_id' => 'Новость',
            'image' => 'Картинка',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews(): ActiveQuery {
        return $this->hasOne(News::class, ['id' => 'news_id']);
    }
}
