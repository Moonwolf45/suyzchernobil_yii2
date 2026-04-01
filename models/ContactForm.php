<?php

namespace app\models;

use himiklab\yii2\recaptcha\ReCaptchaValidator2;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model {

    public $name;
    public $email;
    public $subject;
    public $body;
    public $reCaptcha;

    /**
     * @return array the validation rules.
     */
    public function rules(): array {
        return [
            [['name', 'email', 'subject', 'body'], 'required'],
            ['email', 'email'],
            [['reCaptcha'], ReCaptchaValidator2::class, 'secret' => Yii::$app->params['secretV2'],
                'uncheckedMessage' => 'Пожалуйста подтвердите, что вы не робот.'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array {
        return [
            'name' => 'Имя',
            'email' => 'E-mail',
            'subject' => 'Тема сообщения',
            'body' => 'Текст',
            'reCaptcha' => 'ReCaptcha',
        ];
    }
}
