<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\NewsImage $model */
/** @var app\models\News $news */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="news-image-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'news_id')->widget(Select2::class, [
        'theme' => Select2::THEME_KRAJEE_BS5,
        'data' => ArrayHelper::map($news, 'id', 'title'),
        'options' => ['placeholder' => 'Выберите категорию'],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]); ?>

    <div class="form-group">
        <div class="preview_img">
            <?php if ($model->image !== null): ?>
                <?= Html::img('@web/' . $model->image, ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php else: ?>
                <?= Html::img('@web/images/placeHolder.png', ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php endif; ?>
        </div>
    </div>
    <?= $form->field($model, 'image')->fileInput(['id' => 'image']); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php

$script = <<< JS
    function handleFileSelect(evt) {
        let file = evt.target.files;
        let f = file[0];
        if (!f.type.match('image.*')) {
            alert("Данный файл не изображение!!!");
        }
        let reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                var img = document.getElementById("preview_img");
                console.log(img);
                
                img.src = e.target.result;
            };
        })(f);
        reader.readAsDataURL(f);
    }
    document.getElementById('image').addEventListener('change', handleFileSelect, false);
JS;

$this->registerJs($script); ?>
