<?php

use app\widgets\NewsPopularWidget;
use app\widgets\TagsWidget;

?>

<div class="col-md-3 animate-box" data-animate-effect="fadeInRight">
    <?= TagsWidget::widget(); ?>
    <?= NewsPopularWidget::widget(); ?>
</div>