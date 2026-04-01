<?php

use kartik\select2\Select2;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

mihaildev\elfinder\Assets::noConflict($this);

/** @var yii\web\View $this */
/** @var app\models\News $model */
/** @var app\models\Category $categories */
/** @var app\models\Tag $tags */
/** @var app\models\NewsImage $gallery */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="news-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'category_id')->widget(Select2::class, [
        'theme' => Select2::THEME_KRAJEE_BS5,
        'data' => ArrayHelper::map($categories, 'id', 'title'),
        'options' => ['placeholder' => 'Выберите категорию'],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]); ?>

    <?= $form->field($model, 'tagsLink')->widget(Select2::class, [
        'theme' => Select2::THEME_KRAJEE_BS3,
        'data' => ArrayHelper::map($tags, 'id', 'title'),
        'options' => ['placeholder' => 'Выберите теги', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]); ?>

    <div class="form-group">
        <div class="preview_img">
            <?php if (!empty($model->image)): ?>
                <?= Html::img('@web/' . $model->image, ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php else: ?>
                <?= Html::img('@web/images/placeHolder.png', ['id' => 'preview_img', 'alt' => 'Превью']); ?>
            <?php endif; ?>
        </div>
    </div>
    <?= $form->field($model, 'image')->fileInput(['id' => 'image', 'accept' => Yii::$app->params['mimeTypesImage']]); ?>

    <?= $form->field($model, 'description')->widget(CKEditor::class, [
        'editorOptions' => ElFinder::ckeditorOptions('elfinder')
    ]); ?>

    <?php if(!empty($gallery)): ?>
        <div class="form-group">
            <div class="row">
                <?php foreach($gallery as $image): ?>
                    <div class="gallery" id="<?= $image['id']; ?>_<?= $model->id; ?>">
                        <button type="button" class="close del_gallery" data-href="/admin/news/delete-images"
                                data-model="<?= $model->id; ?>" data-image="<?= $image['id']; ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?= Html::img('@web/' . $image['image'], ['id' => 'preview_bg', 'alt' => $image['id'] . '_' . $model->id,
                            'class' => 'img-thumbnail']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <?= $form->field($model, 'gallery[]')->fileInput(['multiple' => 'true', 'accept' => Yii::$app->params['mimeTypesImage']]); ?>

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
 
    $(".del_gallery").on("click", function (e) {
        e.preventDefault();
        let isTrue = confirm("Удалить изображение?");
        if (isTrue === true) {
            let href = $(this).data('href');
            let id_model = $(this).data('model');
            let image = $(this).data('image');
            $.ajax({
                type: 'POST',
                cache: false,
                url: href + "?id_model=" + id_model + "&image=" + image,
                success: function(data) {
                    if (data.res) {
                        $('#'+image+'_'+id_model).remove();    
                    }
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText + ' | ' + status + ' | ' +error);
                }
            });
        }
    });
JS;

$this->registerJs($script); ?>
