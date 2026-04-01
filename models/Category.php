<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%categories}}".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $meta_keywords
 * @property string|null $meta_description
 * @property int|null $main_status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property News[] $news
 */
class Category extends ActiveRecord {

    private $_url;

    public function getUrl(): string{
        if ($this->_url === null) {
            $this->_url = Yii::$app->urlManager->createUrl(['category/view', 'alias' => $this->slug]);
        }

        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%categories}}';
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
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug'
            ], [
                'class' => CacheBehavior::class,
                'cacheName' => 'Category'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['title'], 'required'],
            [['main_status'], 'integer'],
            [['main_status'], 'default', 'value' => 0],
            [['title', 'slug', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['slug'], 'unique']
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
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'main_status' => 'Выводить на главной'
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return ActiveQuery
     */
    public function getNews(): ActiveQuery {
        return $this->hasMany(News::class, ['category_id' => 'id']);
    }
}
