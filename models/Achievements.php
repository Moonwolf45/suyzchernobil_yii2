<?php

namespace app\models;

use app\behaviors\CacheBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%achievements}}".
 *
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string $file
 * @property int $fasten
 * @property int $isPdf
 * @property int $created_at
 * @property int $updated_at
 */
class Achievements extends ActiveRecord {

    const SCENARIO_INSERT = 'insert';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%achievements}}';
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
                'class' => CacheBehavior::class,
                'cacheName' => 'Documents'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['title', 'file'], 'required', 'on' => self::SCENARIO_INSERT],
            [['title'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['title'], 'string'],
            [['fasten', 'isPdf'], 'boolean'],

            [['image'], 'file', 'extensions' => Yii::$app->params['extensionsImage'],
                'mimeTypes' => Yii::$app->params['mimeTypesImage'], 'maxSize' => 1024 * 1024 * 10],

            [['file'], 'file', 'extensions' => array_merge(Yii::$app->params['extensionsImage'],
                Yii::$app->params['extensionsDocuments']), 'mimeTypes' => array_merge(Yii::$app->params['mimeTypesImage'],
                Yii::$app->params['mimeTypesDocuments']), 'maxSize' => 1024 * 1024 * 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'image' => 'Изображение',
            'file' => 'Документ',
            'fasten' => 'Закрепить',
            'isPdf' => 'Пдф',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения'
        ];
    }
}
