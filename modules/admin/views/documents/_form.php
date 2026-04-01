<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="documents-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

    <div class="form-group">
        <div class="preview_img">
            <?php if ($model->image !== null): ?>
                <?= Html::img('@web/' . $model->image, ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php else: ?>
                <?= Html::img('@web/images/placeHolder.png', ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php endif; ?>
        </div>
    </div>
    <?= $form->field($model, 'image')->fileInput(['id' => 'image', 'accept' => Yii::$app->params['mimeTypesImage']]); ?>

    <?= $form->field($model, 'file')->fileInput(['accept' => array_merge(Yii::$app->params['mimeTypesImage'],
        Yii::$app->params['mimeTypesDocuments'])]); ?>

    <?= $form->field($model, 'fasten')->checkbox(); ?>

    <?= $form->field($model, 'isPdf')->checkbox(); ?>

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
        if (f.type.match('image.*')) {
            let reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    let img = document.getElementById("preview_img");
                    
                    img.src = e.target.result;
                };
            })(f);
            reader.readAsDataURL(f);
        }
    }
    document.getElementById('image').addEventListener('change', handleFileSelect, false);
JS;

$this->registerJs($script); ?>