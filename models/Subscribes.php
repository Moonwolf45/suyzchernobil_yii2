<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%subscribes}}".
 *
 * @property int $id
 * @property string $email
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 */
class Subscribes extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%subscribes}}';
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
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['email'], 'required'],
            [['status'], 'integer'],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'email' => 'E-mail',
            'status' => 'Статус',
        ];
    }
}
