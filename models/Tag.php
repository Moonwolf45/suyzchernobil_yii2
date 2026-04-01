<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%tags}}".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $meta_keywords
 * @property string|null $meta_description
 * @property int $created_at
 * @property int $updated_at
 *
 * @property News[] $news
 * @property TagsNews[] $tagsNews
 */
class Tag extends ActiveRecord {

    private $_url;

    /**
     * @return string
     */
    public function getUrl(): string{
        if ($this->_url === null) {
            $this->_url = Yii::$app->urlManager->createUrl(['tags/view', 'alias' => $this->slug]);
        }

        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%tags}}';
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
                'cacheName' => 'Tag'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['title'], 'required'],
            [['title', 'slug', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
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
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения'
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getNews(): ActiveQuery {
        return $this->hasMany(News::class, ['id' => 'news_id'])
            ->viaTable('{{%tags_news}}', ['tags_id' => 'id']);
    }

    /**
     * Gets query for [[TagsNews]].
     *
     * @return ActiveQuery
     */
    public function getTagsNews(): ActiveQuery {
        return $this->hasMany(TagsNews::class, ['tags_id' => 'id']);
    }
}
