<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use app\jobs\VideoJob;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\httpclient\Client;

/**
 * This is the model class for table "{{%news_video}}".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $meta_keywords
 * @property string|null $meta_description
 * @property string|null $video
 * @property int $views
 * @property int $twisted_views
 * @property string $preview_image
 * @property string $duration
 * @property int $created_at
 * @property int $updated_at
 */
class NewsVideo extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%news_video}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ], [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug'
            ], [
                'class' => CacheBehavior::class,
                'cacheName' => 'NewsVideo'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['title'], 'required'],
            [['views', 'twisted_views'], 'integer'],
            [['title', 'slug', 'meta_keywords', 'meta_description', 'video', 'preview_image', 'duration'], 'string', 'max' => 255],
            [['preview_image', 'duration'], 'default', 'value' => null],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'slug' => 'Транслитерированное название',
            'meta_keywords' => 'Мета-ключевые слова',
            'meta_description' => 'Мета-описание',
            'video' => 'Ссылка на видео',
            'views' => 'Просмотры',
            'preview_image' => 'Картинка для видео',
            'duration' => 'Длительность видео',
            'twisted_views' => 'Накрученные просмотры',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения'
        ];
    }


    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
    }
}
