<?php

namespace app\modules\admin\controllers;


use app\models\News;
use app\models\NewsImage;
use app\models\traits\UploadFilesTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * NewsImageController implements the CRUD actions for NewsImage model.
 */
class NewsImageController extends Controller {
    use UploadFilesTrait;

    /**
     * @inheritDoc
     */
    public function behaviors(): array {
        return array_merge(
            parent::behaviors(), [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all NewsImage models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $dataProvider = new ActiveDataProvider([
            'query' => NewsImage::find(),
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewsImage model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NewsImage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate(): Response|string {
        $news = News::find()->select(['id', 'title'])->asArray()->all();
        $model = new NewsImage();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->image = UploadedFile::getInstance($model, 'image');

                if (!empty($model->image)) {
                    $image = $this->uploadImage($model, 'image', 'news');
                    $model->image = $image;
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('news-image', [['result' => 'success', 'value' => 'Доп. картинка успешно загружена']]);

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('news-image', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model, 'news' => $news]);
    }

    /**
     * Updates an existing NewsImage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id): Response|string {
        $news = News::find()->select(['id', 'title'])->asArray()->all();
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $old_image = $model->image;

            if ($model->load($this->request->post())) {
                $model->image = UploadedFile::getInstance($model, 'image');

                if (!empty($model->image)) {
                    $image = $this->uploadImage($model, 'image', 'news', true, $old_image);
                    $model->image = $image;
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('news-image', [['result' => 'success', 'value' => 'Доп. картинка успешно обновлена']]);

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('news-image', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                }
            }
        }

        return $this->render('update', ['model' => $model, 'news' => $news]);
    }

    /**
     * Deletes an existing NewsImage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     *
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response {
        $newsImage = NewsImage::findOne(['news_id' => $id]);
        $this->deleteImages($newsImage, 'image', 'one');

        if ($newsImage->delete() !== false) {
            Yii::$app->session->setFlash('news-image', [['result' => 'success', 'value' => 'Доп. картинка успешно удалена']]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the NewsImage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return NewsImage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): NewsImage {
        if (($model = NewsImage::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
