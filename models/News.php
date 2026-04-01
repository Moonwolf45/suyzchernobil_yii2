<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\httpclient\Client;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $meta_keywords
 * @property string|null $meta_description
 * @property int $category_id
 * @property string|null $image
 * @property string|null $description
 * @property int $views
 * @property int $twisted_views
 * @property int $created_at
 * @property int $updated_at
 * @property int $published_at_vk
 * @property int $published_at_ok
 *
 * @property Category $category
 * @property NewsImage[] $newsImages
 * @property Tag[] $tags
 * @property TagsNews[] $tagsNews
 */
class News extends ActiveRecord {

    public $gallery;
    public $tagsLink;

    private $_url;

    public function getUrl(): string{
        if ($this->_url === null) {
            $this->_url = Yii::$app->urlManager->createUrl(['news/view', 'category_alias' => $this->category->slug,
                'alias' => $this->slug]);
        }

        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%news}}';
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
                'cacheName' => 'News'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['title', 'category_id'], 'required'],
            [['category_id', 'views', 'twisted_views', 'created_at', 'updated_at', 'published_at_vk', 'published_at_ok'], 'integer'],
            [['views', 'twisted_views'], 'default', 'value' => 0],
            [['published_at_vk', 'published_at_ok'], 'default', 'value' => null],
            [['description'], 'string'],
            [['tagsLink'], 'safe'],
            [['title', 'slug', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class,
                'targetAttribute' => ['category_id' => 'id']],

            [['image'], 'file', 'extensions' => Yii::$app->params['extensionsImage'], 'maxSize' => 1024 * 1024 * 8],
            [['gallery'], 'file', 'extensions' => Yii::$app->params['extensionsImage'], 'maxFiles' => 15,
                'maxSize' => 1024 * 1024 * 8],
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
            'category_id' => 'Категория',
            'tags' => 'Теги',
            'tagsLink' => 'Теги',
            'image' => 'Изображение',
            'gallery' => 'Дополнительные картинки',
            'description' => 'Описание',
            'views' => 'Просмотры',
            'twisted_views' => 'Накрученные просмотры',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'published_at_vk' => 'Дата публикации в вк',
            'published_at_ok' => 'Дата публикации в одноклассниках'
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[NewsImages]].
     *
     * @return ActiveQuery
     */
    public function getNewsImages(): ActiveQuery {
        return $this->hasMany(NewsImage::class, ['news_id' => 'id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return ActiveQuery
     *
     * @throws InvalidConfigException
     */
    public function getTags(): ActiveQuery {
        return $this->hasMany(Tag::class, ['id' => 'tags_id'])
            ->viaTable('{{%tags_news}}', ['news_id' => 'id']);
    }

    /**
     * Gets query for [[TagsNews]].
     *
     * @return ActiveQuery
     */
    public function getTagsNews(): ActiveQuery {
        return $this->hasMany(TagsNews::class, ['news_id' => 'id']);
    }
}
