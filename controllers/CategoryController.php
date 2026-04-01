<?php

namespace app\controllers;


use app\models\Category;
use app\models\News;
use app\models\traits\MetaTrait;
use yii\data\Pagination;
use yii\web\Controller;
use yii\filters\VerbFilter;

class CategoryController extends Controller {
    use MetaTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $alias
     * @param int $page
     *
     * @return string
     */
    public function actionView($alias, int $page = 1): string {
        $category = Category::find()->where(['slug' => $alias])->asArray()->one();

        $this->setMeta($this, $category['title'], $category['meta_keywords'], $category['meta_description']);

        $query = News::find()->joinWith(['category'])->where([News::tableName() . '.category_id' => $category['id']]);
        $countQuery = clone $query;
        $pages = new Pagination(['defaultPageSize' => 5, 'totalCount' => $countQuery->count()]);
        $news = $query->offset($pages->offset)->limit($pages->limit)->orderBy([News::tableName() . '.id' => SORT_DESC])
            ->asArray()->all();

        return $this->render('view', compact('news', 'pages', 'category'));
    }
}
