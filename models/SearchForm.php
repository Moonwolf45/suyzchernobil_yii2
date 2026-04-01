<?php

namespace app\models;


use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class SearchForm extends Model {

    public $q;

    /**
     * @return array the validation rules.
     */
    public function rules(): array {
        return [
            [['q'], 'required'],
            [['q'], 'trim'],
            [['q'], 'string', 'max' => 255]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array {
        return [
            'q' => 'Текст запроса',
        ];
    }
}
