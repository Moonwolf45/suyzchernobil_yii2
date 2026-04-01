<?php

namespace app\modules\admin\controllers;


use app\models\Achievements;
use app\models\traits\UploadFilesTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * AchievementsController implements the CRUD actions for Documents model.
 */
class AchievementsController extends Controller {
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
     * Lists all Achievements models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $dataProvider = new ActiveDataProvider([
            'query' => Achievements::find(),
            'pagination' => [
                'pageSize' => 15
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
     * Displays a single Achievements model.
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
     * Creates a new Achievements model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate(): Response|string {
        $model = new Achievements();
        $model->setScenario(Achievements::SCENARIO_INSERT);

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->load($this->request->post())) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    $model->file = UploadedFile::getInstance($model, 'file');

                    if (!empty($model->image)) {
                        $image = $this->uploadImage($model, 'image', 'achievements', false);
                        $model->image = $image;
                    } else {
                        $model->image = null;
                    }

                    if (!empty($model->file)) {
                        $file = $this->uploadImage($model, 'file', 'achievements', false);
                        $model->file = $file;
                    }

                    if ($model->save()) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('achievements', [['result' => 'success', 'value' => 'Достижение успешно загружено']]);

                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('achievements', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();

                throw $e;
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Achievements model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     *
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id): Response|string {
        $model = $this->findModel($id);
        $model->setScenario(Achievements::SCENARIO_UPDATE);

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $old_image = $model->image;
                $old_file = $model->file;

                if ($model->load($this->request->post())) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    $model->file = UploadedFile::getInstance($model, 'file');

                    if (!empty($model->image)) {
                        $image = $this->uploadImage($model, 'image', 'achievements', false, $old_image);
                        $model->image = $image;
                    } else {
                        $model->image = $old_image;
                    }

                    if (!empty($model->file)) {
                        $file = $this->uploadImage($model, 'file', 'achievements', false, $old_file);
                        $model->file = $file;
                    } else {
                        $model->file = $old_file;
                    }

                    if ($model->save()) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('achievements', [['result' => 'success', 'value' => 'Достижение успешно обновлено']]);

                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('achievements', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();

                throw $e;
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Achievements model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     *
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $achievement = Achievements::find()->where(['id' => $id])->one();
            $this->deleteImages($achievement, 'image', 'one');
            $this->deleteImages($achievement, 'file', 'one');
            if ($achievement->delete() !== false) {
                $transaction->commit();

                Yii::$app->session->setFlash('achievements', [['result' => 'success', 'value' => 'Достижение успешно удалено']]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('achievements', [['result' => 'error', 'value' => 'Произошла неизвестная ошибка']]);

            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Achievements model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Achievements the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Achievements {
        if (($model = Achievements::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
