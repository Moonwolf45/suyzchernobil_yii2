<?php

namespace app\jobs;

use app\models\News;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\queue\JobInterface;

/**
 * Базовый класс для джобов публикации в соцсетях
 */
abstract class SocialPublishJob extends BaseObject implements JobInterface
{
    public $news_id;

    /**
     * Максимальное количество картинок для загрузки
     */
    protected const MAX_IMAGES = 10;

    /**
     * Количество попыток при ошибке API
     */
    protected const MAX_RETRIES = 5;

    /**
     * Задержка между попытками (мс)
     */
    protected const RETRY_DELAY = 1000;

    /**
     * @var string Поле для отметки времени публикации (например 'published_at_vk')
     */
    protected abstract function getPublishedAtField(): string;

    /**
     * @var string Название соцсети для логирования
     */
    protected abstract function getSocialNetworkName(): string;

    /**
     * @param Client $client
     * @param News $news
     * @return bool Успешно ли выполнена публикация
     */
    protected abstract function publish(Client $client, News $news): bool;

    /**
     * @param Client $client
     * @param array $images
     * @return array Массив загруженных изображений (формат зависит от соцсети)
     */
    protected abstract function uploadImages(Client $client, array $images): array;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        try {
            $news = $this->getNews();

            if (!$news) {
                Yii::warning("Новость с ID {$this->news_id} не найдена", 'jobs-social');
                return;
            }

            $client = new Client();

            $published = $this->publish($client, $news);

            if ($published) {
                $this->markAsPublished($news);
            }

        } catch (\Throwable $e) {
            Yii::error("Ошибка при публикации в {$this->getSocialNetworkName()}: " . $e->getMessage(), 'jobs-social');
            Yii::error($e->getTraceAsString(), 'jobs-social');

            // Не выбрасываем исключение, чтобы не ломать очередь
            // Но логируем ошибку для последующего анализа
        }
    }

    /**
     * Получает новость из БД
     *
     * @return News|null
     */
    protected function getNews(): ?News
    {
        return News::find()
            ->joinWith(['category', 'newsImages', 'tags'])
            ->where([News::tableName() . '.id' => $this->news_id])
            ->one();
    }

    /**
     * Подготавливает текст сообщения из описания новости
     *
     * @param News $news
     * @return string
     */
    protected function prepareMessage(News $news): string
    {
        $message = str_replace('&nbsp;', ' ', $news->description);
        $message = strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
        $message = trim(preg_replace('/\s+/', ' ', $message));
        $message = preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $message) . "\r\n\r\n";

        $countTag = 0;
        if (!empty($news->tags)) {
            foreach ($news->tags as $tag) {
                $message .= $countTag === 0 ? '#' . $tag['title'] : ', #' . $tag['title'];
                $countTag++;
            }
        }

        $message .= "\r\n\r\n";
        $message .= Url::to([
            'news/view',
            'category_alias' => $news->category['slug'],
            'alias' => $news->slug
        ], true);

        return $message;
    }

    /**
     * Получает список изображений для загрузки (основная + дополнительные)
     *
     * @param News $news
     * @return array
     */
    protected function getImages(News $news): array
    {
        $images = [];

        if (!empty($news->image)) {
            $images[] = $news->image;
        }

        $countImage = 1;
        if (!empty($news->newsImages)) {
            foreach ($news->newsImages as $image) {
                if ($countImage >= self::MAX_IMAGES) {
                    break;
                }
                if (!empty($image['image'])) {
                    $images[] = $image['image'];
                    $countImage++;
                }
            }
        }

        return $images;
    }

    /**
     * Отмечает новость как опубликованную
     *
     * @param News $news
     */
    protected function markAsPublished(News $news): void
    {
        $field = $this->getPublishedAtField();
        $news->$field = time();

        if (!$news->save(false)) {
            Yii::error("Не удалось сохранить время публикации: " . json_encode($news->errors), 'jobs-social');
        }
    }

    /**
     * Выполняет запрос с повторными попытками при ошибке
     *
     * @param callable $requestCallback Функция, выполняющая запрос
     * @param string $operationName Название операции для логирования
     *
     * @return mixed|null Результат запроса или null при неудаче
     */
    protected function executeWithRetry(callable $requestCallback, string $operationName): mixed
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $result = $requestCallback();

                if ($result !== null) {
                    return $result;
                }

            } catch (\Exception $e) {
                $lastException = $e;
                Yii::warning(
                    "Попытка {$attempt} не удалась ({$operationName}): " . $e->getMessage(),
                    'jobs-social'
                );
            }

            if ($attempt < self::MAX_RETRIES) {
                usleep(self::RETRY_DELAY * 1000);
            }
        }

        if ($lastException) {
            Yii::error(
                "Все попытки исчерпаны ({$operationName}): " . $lastException->getMessage(),
                'jobs-social'
            );
        }

        return null;
    }

    /**
     * Проверяет существование файла изображения
     *
     * @param string $imagePath
     * @return bool
     */
    protected function imageExists(string $imagePath): bool
    {
        $fullPath = Yii::getAlias('@app/web/' . $imagePath);

        return file_exists($fullPath) && is_readable($fullPath);
    }
}
