<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%documents}}".
 *
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string|null $image
 * @property string $file
 * @property int $fasten
 * @property int $isPdf
 * @property int $created_at
 * @property int $updated_at
 */
class Documents extends ActiveRecord
{
    public const SCENARIO_INSERT = 'insert';
    public const SCENARIO_UPDATE = 'update';

    public const DOCUMENT_TYPE_BOOK_OF_MEMORY = 'book_of_memory';
    public const DOCUMENT_TYPE_SOVIET_UNION_LAST_BATTLE = 'the_soviet_union_last_battle';

    public const DOCUMENT_TYPES = [
        self::DOCUMENT_TYPE_BOOK_OF_MEMORY => 'Книга памяти',
        self::DOCUMENT_TYPE_SOVIET_UNION_LAST_BATTLE => 'Последняя битва Советского Союза'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%documents}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ], [
                'class' => CacheBehavior::class,
                'cacheName' => 'Documents'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'file'], 'required', 'on' => self::SCENARIO_INSERT],
            [['title'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['title', 'type'], 'string'],
            [['fasten', 'isPdf'], 'boolean'],

            [['image'], 'file', 'extensions' => Yii::$app->params['extensionsImage'],
                'mimeTypes' => Yii::$app->params['mimeTypesImage'], 'maxSize' => 1024 * 1024 * 10],

            [['file'], 'file', 'extensions' => array_merge(
                Yii::$app->params['extensionsImage'],
                Yii::$app->params['extensionsDocuments']
            ), 'mimeTypes' => array_merge(
                Yii::$app->params['mimeTypesImage'],
                Yii::$app->params['mimeTypesDocuments']
            ), 'maxSize' => 1024 * 1024 * 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'type' => 'Тип',
            'image' => 'Изображение',
            'file' => 'Документ',
            'fasten' => 'Закрепить',
            'isPdf' => 'Пдф',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения'
        ];
    }
}
