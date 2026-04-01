<?php

namespace app\modules\admin\controllers;


use app\jobs\VideoJob;
use app\models\NewsVideo;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;


/**
 * NewsVideoController implements the CRUD actions for NewsVideo model.
 */
class NewsVideoController extends Controller {
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
     * Lists all NewsVideo models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $dataProvider = new ActiveDataProvider([
            'query' => NewsVideo::find(),
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewsVideo model.
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
     * Creates a new NewsVideo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate(): Response|string {
        $model = new NewsVideo();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                if ($model->save()) {
                    Yii::$app->queue->push(new VideoJob(['video_id' => $model->id]));
                    Yii::$app->session->setFlash('news-video', [['result' => 'success', 'value' => 'Видео-новость успешна создана']]);

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('news-video', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NewsVideo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id): Response|string {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            if ($model->save()) {
                Yii::$app->queue->push(new VideoJob(['video_id' => $model->id]));
                Yii::$app->session->setFlash('news-video', [['result' => 'success', 'value' => 'Видео-новость успешно обновлена']]);

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('news-video', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NewsVideo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     *
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response {
        if ($this->findModel($id)->delete() !== false) {
            Yii::$app->session->setFlash('news-video', [['result' => 'success', 'value' => 'Видео-новость успешно удалена']]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the NewsVideo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     *
     * @return NewsVideo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): NewsVideo {
        if (($model = NewsVideo::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
