<?php

/** @var app\models\Category $categories */

use yii\helpers\Url;

?>

<div class="col-12 col-md-3 col-lg-2">
    <?php if (!empty($categories)): ?>
        <div class="footer_main_title py-3">Категории</div>
        <ul class="footer_menu">
            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="<?= Url::to(['category/view', 'alias' => $category['slug']]); ?>" class="">
                        <i class="fa fa-angle-right"></i>&nbsp;&nbsp; <?= $category['title']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>



