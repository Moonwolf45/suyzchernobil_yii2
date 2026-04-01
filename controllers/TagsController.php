<?php

namespace app\controllers;


use app\models\News;
use app\models\Tag;
use app\models\traits\MetaTrait;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TagsController extends Controller {
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
        $tag = Tag::find()->where(['slug' => $alias])->asArray()->one();

        $this->setMeta($this, $tag['title'], $tag['meta_keywords'], $tag['meta_description']);

        $query = News::find()->joinWith(['category', 'tags'])->where([Tag::tableName() . '.slug' => $alias]);
        $countQuery = clone $query;
        $pages = new Pagination(['defaultPageSize' => 5, 'totalCount' => $countQuery->count()]);
        $news = $query->offset($pages->offset)->limit($pages->limit)->orderBy([News::tableName() . '.id' => SORT_DESC])
            ->asArray()->all();

        return $this->render('view', compact('news', 'pages', 'tag'));
    }
}
