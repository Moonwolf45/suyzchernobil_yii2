<?php

/** @var app\models\ContactForm $model */

use app\widgets\Alert;
use app\widgets\BreadcrumbsSchemaWidget;
use himiklab\yii2\recaptcha\ReCaptcha2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Контакты'];
?>
<div class="container-fluid contact_us_bg_img">
    <div class="container">
        <div class="row mx-0">
            <?= BreadcrumbsSchemaWidget::widget([
                'links' => $this->params['breadcrumbs'] ?? [],
            ]); ?>
        </div>
        <div class="row mx-0">
            <?php if (Yii::$app->session->getFlash('contact')): ?>
                <?= Alert::widget(['key' => 'contact']); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="container-fluid  fh5co_fh5co_bg_contcat" itemscope itemtype="https://schema.org/Organization">
    <div class="container">
        <div class="row py-4">
            <div class="col-md-4 py-3">
                <div class="row fh5co_contact_us_no_icon_difh5co_hover">
                    <div class="col-3 fh5co_contact_us_no_icon_difh5co_hover_1">
                        <div class="fh5co_contact_us_no_icon_div">
                            <span>
                                <i class="fa fa-phone"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-9 align-self-center fh5co_contact_us_no_icon_difh5co_hover_2">
                        <span class="c_g d-block">Номер телефона</span>
                        <a href="tel:+79824208616" class="d-block c_g fh5co_contact_us_no_text" itemprop="telephone">
                            +7 (982) 420-86-16
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-4 py-3">
                <div class="row fh5co_contact_us_no_icon_difh5co_hover">
                    <div class="col-3 fh5co_contact_us_no_icon_difh5co_hover_1">
                        <div class="fh5co_contact_us_no_icon_div"> <span><i class="fa fa-envelope"></i></span> </div>
                    </div>
                    <div class="col-9 align-self-center fh5co_contact_us_no_icon_difh5co_hover_2">
                        <span class="c_g d-block">Есть еще вопросы?</span>
                        <a href="mailto:chernobil45@mail.ru" class="d-block c_g fh5co_contact_us_no_text" itemprop="email">
                            chernobil45@mail.ru
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-4 py-3" itemscope itemprop="address" itemtype="https://schema.org/PostalAddress">
                <div class="row fh5co_contact_us_no_icon_difh5co_hover">
                    <div class="col-3 fh5co_contact_us_no_icon_difh5co_hover_1">
                        <div class="fh5co_contact_us_no_icon_div">
                            <span>
                                <i class="fa fa-map-marker"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-9 align-self-center fh5co_contact_us_no_icon_difh5co_hover_2">
                        <span class="c_g d-block">Адрес</span>

                        <div class="d-block c_g fh5co_contact_us_no_text">
                            <span itemprop="postalCode">640002</span>,
                            <span itemprop="addressLocality">г. Курган</span>,
                            <span itemprop="streetAddress">ул. Максима Горького, д.35</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="container-fluid mb-4">
    <div class="container">
        <div class="col-12 text-center contact_margin_svnit">
            <div class="text-center fh5co_heading py-2">Связаться с нами</div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <?php $form = ActiveForm::begin(['method' => 'post', 'options' => ['class' => 'row'],
                    'fieldConfig' => ['options' => ['class' => 'col-6 py-3']]]); ?>

                    <?= $form->field($model, 'name', ['options' => ['class' => 'col-12 py-3']])
                        ->textInput(['placeholder' => 'Введите ваше имя', 'class' => 'form-control fh5co_contact_text_box'])
                        ->label(false); ?>

                    <?= $form->field($model, 'email')->textInput(['placeholder' => 'Введите ваш e-mail',
                        'class' => 'form-control fh5co_contact_text_box'])->label(false); ?>

                    <?= $form->field($model, 'subject')->textInput(['placeholder' => 'Введите тему письма',
                        'class' => 'form-control fh5co_contact_text_box'])->label(false); ?>

                    <?= $form->field($model, 'body', ['options' => ['class' => 'col-12 py-3']])
                        ->textarea(['rows' => '6', 'placeholder' => 'Текст письма', 'form-control fh5co_contacts_message'])
                        ->label(false); ?>

                    <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha2::class, [
                        'siteKey' => Yii::$app->params['siteKeyV2'],
                    ])->label(false); ?>

                    <div class="col-12 py-3 text-center">
                        <?= Html::submitButton('Отправить', ['class' => 'btn contact_btn']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-12 col-md-6 mt-3">
                <iframe src="https://yandex.ru/map-widget/v1/?ll=65.339615%2C55.433961&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1NjQ0ODMzNhJK0KDQvtGB0YHQuNGPLCDQmtGD0YDQs9Cw0L0sINGD0LvQuNGG0LAg0JzQsNC60YHQuNC80LAg0JPQvtGA0YzQutC-0LPQviwgMzUiCg2xrYJCFVi8XUI%2C&z=17.89" class="map_sss" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>