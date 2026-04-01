<?php

namespace app\modules\admin\controllers;


use app\jobs\OkPublishJob;
use app\jobs\VkPublishJob;
use app\models\Category;
use app\models\News;
use app\models\NewsImage;
use app\models\Tag;
use app\models\TagsNews;
use app\models\traits\UploadFilesTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller {
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
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all News models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $dataProvider = new ActiveDataProvider([
            'query' => News::find()->joinWith(['category', 'tags'])->groupBy([News::tableName() . '.id']),
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
     * Displays a single News model.
     * @param int $id ID
     *
     * @return string
     */
    public function actionView(int $id): string {
        $news = News::find()->joinWith(['category', 'tags'])->where([News::tableName() . '.id' => $id])->one();

        $gallery = new ActiveDataProvider([
            'query' => NewsImage::find()->where(['news_id' => $id]),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('view', ['model' => $news, 'gallery' => $gallery]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     * @throws Exception|\ImagickException
     */
    public function actionCreate(): Response|string {
        $categories = Category::find()->asArray()->all();
        $tags = Tag::find()->indexBy('id')->all();
        $model = new News();

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->load($this->request->post())) {
                    $description = strip_tags($model->description);
                    $arrayDescription = explode('.', $description);
                    $meta = str_replace(['&nbsp;', '&mdash;'], [' ', '-'], $arrayDescription[0]);

                    if (empty($model->meta_keywords)) {
                        $keywords = str_replace([' - ', '- ', '; ', '. ', ', ', '! ', '? ', ': ', ';', '.', '!', '?', '-', ':'], ',', $meta);
                        $model->meta_keywords = str_replace(' ', ', ', $keywords);
                    }

                    if (empty($model->meta_description)) {
                        $model->meta_description = $meta . '.';
                    }

                    $model->image = UploadedFile::getInstance($model, 'image');
                    $model->gallery = UploadedFile::getInstances($model, 'gallery');

                    if (!empty($model->image)) {
                        $image = $this->uploadImage($model, 'image', 'news');
                        $model->image = $image;
                    }

                    if ($model->save()) {
                        $tagsLink = $model->tagsLink;
                        if (!empty($tagsLink)) {
                            for ($i = 0; $i < count($tagsLink); $i++) {
                                $model->link('tags', $tags[$tagsLink[$i]]);
                            }
                        }

                        if (!empty($model->gallery)) {
                            $news_gallery = $this->uploadGallery($model, 'gallery', 'news');
                            foreach ($news_gallery as $news_image) {
                                $images = new NewsImage();
                                $images->news_id = $model->id;
                                $images->image = $news_image['path'];
                                $images->save();
                            }
                        }

                        $transaction->commit();

                        Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость успешно создана']]);

                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('news', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();

                throw $e;
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', compact('model', 'categories', 'tags'));
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     *
     * @return string|Response
     * @throws Exception
     * @throws StaleObjectException
     */
    public function actionUpdate(int $id): Response|string {
        $categories = Category::find()->asArray()->all();
        $tags = Tag::find()->indexBy('id')->all();
        $gallery = NewsImage::find()->where(['news_id' => $id])->asArray()->all();
        $model = News::find()->where([News::tableName() . '.id' => $id])->one();
        $tagsLink = TagsNews::find()->select(['tags_id AS id'])->where(['news_id' => $id])->asArray()->all();
        foreach ($tagsLink as $tag) {
            $model->tagsLink[] = $tag['id'];
        }

        if ($this->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $old_image = $model->image;
                $old_tagsLink = $model->tagsLink;

                if ($model->load($this->request->post())) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    $model->gallery = UploadedFile::getInstances($model, 'gallery');

                    if (!empty($model->image)) {
                        $image = $this->uploadImage($model, 'image', 'news', true, $old_image);
                        $model->image = $image;
                    } else {
                        $model->image = $old_image;
                    }

                    $tagsLink = $model->tagsLink;
                    if (!empty($tagsLink)) {
                        $delete_tagsLink = array_diff($old_tagsLink, $tagsLink);
                        $add_tagsLink = array_diff($tagsLink, $old_tagsLink);

                        if (!empty($delete_tagsLink)) {
                            for ($i = 0; $i < count($delete_tagsLink); $i++) {
                                $model->unlink('tags', $tags[$delete_tagsLink[$i]]);
                            }
                        }

                        if (!empty($add_tagsLink)) {
                            for ($i = 0; $i < count($add_tagsLink); $i++) {
                                $model->link('tags', $tags[$add_tagsLink[$i]]);
                            }
                        }
                    }

                    if ($model->save()) {
                        if (!empty($model->gallery)) {
                            $news_gallery = $this->uploadGallery($model, 'gallery', 'news');
                            foreach ($news_gallery as $news_image) {
                                $images = new NewsImage();
                                $images->news_id = $model->id;
                                $images->image = $news_image['path'];
                                $images->save();
                            }
                        }

                        $transaction->commit();

                        Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость успешно обновлена']]);

                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('news', [['result' => 'error', 'value' => 'В форме найдены ошибки']]);
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();

                throw $e;
            }
        }

        return $this->render('update', compact('model', 'categories', 'tags', 'gallery'));
    }


    /**
     * @param $id_model
     * @param $image
     *
     * @return false|string
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDeleteImages($id_model, $image): bool|string {
        $news_image = NewsImage::find()->where(['id' => $image, 'news_id' => $id_model])->one();

        if (@unlink($news_image->image)) {
            $news_image->delete();

            $res = true;
        } else {
            $res = false;
        }

        return json_encode(['res' => $res]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     *
     * @return Response
     * @throws Exception
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete(int $id): Response {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $news = News::find()->where(['id' => $id])->one();
            $gallery = NewsImage::findAll(['news_id' => $id]);
            $this->deleteImages($news, 'image', 'one');
            $this->deleteImages($gallery, 'image');
            if ($news->delete() !== false) {
                $transaction->commit();

                Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость успешно удалена']]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('news', [['result' => 'error', 'value' => 'Произошла неизвестная ошибка']]);

            throw $e;
        }

        return $this->redirect(['index']);
    }

    public function actionPublish(int $id): Response {
        Yii::$app->queue->push(new VkPublishJob(['news_id' => $id]));
        Yii::$app->queue->push(new OkPublishJob(['news_id' => $id]));

        Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость поставлена в очередь на публикацию, 
            в течении 10 минут она появится в соц. сетях']]);

        return $this->redirect(['index']);
    }

    public function retryVkPublish(int $id): Response {
        Yii::$app->queue->push(new VkPublishJob(['news_id' => $id]));

        Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость поставлена в очередь на публикацию, 
            в течении 10 минут она появится в соц. сетях']]);

        return $this->redirect(['index']);
    }

    public function retryOkPublish(int $id): Response {
        Yii::$app->queue->push(new OkPublishJob(['news_id' => $id]));

        Yii::$app->session->setFlash('news', [['result' => 'success', 'value' => 'Новость поставлена в очередь на публикацию, 
            в течении 10 минут она появится в соц. сетях']]);

        return $this->redirect(['index']);
    }
}
