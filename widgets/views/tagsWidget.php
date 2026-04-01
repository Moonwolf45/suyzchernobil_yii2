<?php

/** @var app\models\Tag $tags */

use yii\helpers\Url;

?>

<?php if (!empty($tags)): ?>
    <div>
        <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4">Теги</div>
    </div>
    <div class="clearfix"></div>
    <div class="fh5co_tags_all">
        <?php foreach ($tags as $tag): ?>
            <a href="<?= Url::to(['tags/view', 'alias' => $tag['slug']]); ?>" class="fh5co_tagg">
                <?= $tag['title']; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
