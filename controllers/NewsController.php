<?php

namespace app\controllers;


use app\models\Achievements;
use app\models\Category;
use app\models\Documents;
use app\models\News;
use app\models\NewsVideo;
use app\models\SearchForm;
use app\models\Subscribes;
use app\models\traits\MailToUserTrait;
use app\models\traits\MetaTrait;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class NewsController extends Controller {
    use MetaTrait;
    use MailToUserTrait;

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
     * {@inheritdoc}
     */
    public function actions(): array {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string {
        $this->setMeta($this);

        $news = News::find()->joinWith(['category'])->orderBy([News::tableName() . '.id' => SORT_DESC])->limit(10)
            ->asArray()->all();

        return $this->render('index', compact('news'));
    }

    /**
     * @param $q
     * @param int $page
     *
     * @return string
     */
    public function actionSearch($q = null, int $page = 1): string {
        $news = [];
        $pages = [];

        $model = new SearchForm();
        if ($q !== null) {
            $query = News::find()->joinWith(['category'])->where(['LIKE', News::tableName() . '.title', $q])
                ->orWhere(['LIKE', News::tableName() . '.meta_keywords', $q])
                ->orWhere(['LIKE', News::tableName() . '.meta_description', $q])
                ->orWhere(['LIKE', News::tableName() . '.description', $q]);
            $countQuery = clone $query;
            $pages = new Pagination(['defaultPageSize' => 5, 'totalCount' => $countQuery->count()]);
            $news = $query->offset($pages->offset)->limit($pages->limit)->orderBy([News::tableName() . '.id' => SORT_DESC])
                ->asArray()->all();
        }

        $this->setMeta($this);

        return $this->render('search', compact('model', 'q', 'news', 'pages'));
    }

    /**
     * @param $category_alias
     * @param $alias
     *
     * @return string
     */
    public function actionView($category_alias, $alias): string {
        $news = News::find()->joinWith(['category', 'newsImages', 'tags'])->where([News::tableName() . '.slug' => $alias])->one();
        $news->views += 1;
        $news->twisted_views += rand(5, 15);
        $news->save();

        $this->setMeta($this, $news['title'], $news['meta_keywords'], $news['meta_description'], $news['image']);

        return $this->render('view', compact('news'));
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function actionArchive(int $page = 1): string {
        $categories = Category::find()->orderBy([Category::tableName() . '.title' => SORT_ASC])->asArray()->all();

        $query = News::find()->joinWith(['category']);
        $countQuery = clone $query;
        $pages = new Pagination(['defaultPageSize' => 5, 'totalCount' => $countQuery->count()]);
        $news = $query->offset($pages->offset)->limit($pages->limit)->orderBy([News::tableName() . '.id' => SORT_DESC])
            ->asArray()->all();

        $this->setMeta($this, null, null, 'Исчерпывающая, новостная информация о ежедневной деятельности организации.');

        return $this->render('archive', compact('categories', 'news', 'pages'));
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function actionVideos(int $page = 1): string {
        $query = NewsVideo::find();
        $countQuery = clone $query;
        $pages = new Pagination(['defaultPageSize' => 5, 'totalCount' => $countQuery->count()]);
        $newsVideo = $query->offset($pages->offset)->limit($pages->limit)->orderBy([NewsVideo::tableName() . '.id' => SORT_DESC])
            ->asArray()->all();

        $this->setMeta($this);

        return $this->render('videos', compact('newsVideo', 'pages'));
    }

    /**
     *
     * @return string
     */
    public function actionDocuments(): string {
        $this->setMeta($this, null, 'Регистрационные документы, устав, нормативно-правовые и законодательные базы, сертификаты');

        $documents = Documents::find()->orderBy([Documents::tableName() . '.fasten' => SORT_DESC,
            Documents::tableName() . '.id' => SORT_DESC])->asArray()->all();

        return $this->render('documents', compact('documents'));
    }

    /**
     *
     * @return string
     */
    public function actionOurAchievements(): string {
        $this->setMeta($this, null, 'Успехи, достижения, награды');

        $ourAchievements = Achievements::find()->orderBy([Achievements::tableName() . '.fasten' => SORT_DESC,
            Achievements::tableName() . '.id' => SORT_DESC])->asArray()->all();

        return $this->render('our-achievements', compact('ourAchievements'));
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $this->sendMailToUser(Yii::$app->params['adminEmail'],
                'contact', $model->subject, ['name' => $model->name, 'email' => $model->email, 'body' => $model->body])) {

            Yii::$app->session->setFlash('contact', [['result' => 'success', 'value' => 'Письмо успешно отправлено']]);

            return $this->refresh();
        }

        $this->setMeta($this, null, 'Юридический адрес, телефоны, e-mail, сайт, соцсети, локация');

        return $this->render('contact', compact('model'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return Response
     */
    public function actionSubscribe(): Response {
        $postSubscribe = Yii::$app->request->post();

        $subscribe = Subscribes::find()->where(['email' => $postSubscribe['email']])->asArray()->one();
        if (!empty($subscribe)) {
            Yii::$app->session->setFlash('subscribe', [['result' => 'error', 'value' => 'Вы уже подписаны на рассылку.']]);
        } else {
            $model = new Subscribes();
            $model->email = $postSubscribe['email'];
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('subscribe', [['result' => 'success', 'value' => 'Вы успешно подписались на рассылку.']]);
            } else {
                Yii::$app->session->setFlash('subscribe', [['result' => 'error', 'value' => $model->getErrors()]]);
            }
        }

        return $this->goBack();
    }
}
