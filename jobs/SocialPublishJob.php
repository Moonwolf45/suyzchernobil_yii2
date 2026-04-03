<?php

namespace app\jobs;

use app\models\News;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
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
     * @param int &$uploadedImagesCount Счётчик загруженных изображений (выходной параметр)
     * @param int &$failedImagesCount Счётчик неудачных загрузок (выходной параметр)
     * @return bool Успешно ли выполнена публикация
     */
    protected abstract function publish(Client $client, News $news, int &$uploadedImagesCount = 0, int &$failedImagesCount = 0): bool;

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
        $news = null;
        $uploadedImagesCount = 0;
        $failedImagesCount = 0;

        try {
            $news = $this->getNews();

            if (!$news) {
                Yii::warning("Новость с ID {$this->news_id} не найдена", 'jobs-social');
                $this->sendErrorNotification(null, "Новость с ID {$this->news_id} не найдена в базе данных");

                return;
            }

            $client = new Client(['transport' => new CurlTransport()]);

            $published = $this->publish($client, $news, $uploadedImagesCount, $failedImagesCount);

            if ($published) {
                $this->markAsPublished($news);
                $this->sendSuccessNotification($news, $uploadedImagesCount, $failedImagesCount);
            } else {
                $this->sendErrorNotification($news, "Публикация не удалась - API вернуло ошибку");
            }

        } catch (\Throwable $e) {
            Yii::error("Ошибка при публикации в {$this->getSocialNetworkName()}: " . $e->getMessage(), 'jobs-social');
            Yii::error($e->getTraceAsString(), 'jobs-social');

            $this->sendErrorNotification($news, $e->getMessage());
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

    /**
     * Отправляет уведомление в Telegram
     *
     * @param string $message Текст сообщения
     * @param bool $isError Это ошибка (красное сообщение)
     *
     * @return void
     */
    protected function sendTelegramNotification(string $message, bool $isError = false): void
    {
        if (empty(Yii::$app->params['telegramDeveloperBotToken']) || empty(Yii::$app->params['telegramDeveloperChatId'])) {
            return;
        }

        try {
            $emoji = $isError ? '❌' : '✅';
            $fullMessage = "{$emoji} <b>Публикация в {$this->getSocialNetworkName()}</b> \r\n{$message}";

            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl("https://api.telegram.org/bot" . Yii::$app->params['telegramDeveloperBotToken'] . "/sendMessage")
                ->setData([
                    'chat_id' => Yii::$app->params['telegramDeveloperChatId'],
                    'text' => $fullMessage,
                    'parse_mode' => 'HTML'
                ])
                ->send();

            if (!$response->isOk) {
                Yii::error("Не удалось отправить уведомление в Telegram: " . $response->data['description'] ?? 'Unknown error', 'jobs-social');
            }

        } catch (\Throwable $e) {
            Yii::error("Ошибка отправки уведомления в Telegram: " . $e->getMessage(), 'jobs-social');
        }
    }

    /**
     * Отправляет уведомление об успешной публикации
     *
     * @param News $news
     * @param int $uploadedImagesCount Количество загруженных изображений
     * @param int $failedImagesCount Количество неудачных загрузок
     *
     * @return void
     */
    protected function sendSuccessNotification(News $news, int $uploadedImagesCount = 0, int $failedImagesCount = 0): void
    {
        $message = "<b>Новость:</b> {$news->title} \r\n";
        $message .= "<b>ID:</b> {$this->news_id} \r\n";
        $message .= "<b>Изображений:</b> загружено {$uploadedImagesCount} \r\n";

        if ($failedImagesCount > 0) {
            $message .= " (ошибок: {$failedImagesCount}) ⚠️ \r\n";
        }

        $message .= "🔗 <a href='" . Url::to(['news/view', 'category_alias' => $news->category['slug'], 'alias' => $news->slug], true)
            . "'>Открыть новость</a>";

        $this->sendTelegramNotification($message, false);
    }

    /**
     * Отправляет уведомление об ошибке публикации
     *
     * @param News|null $news
     * @param string $errorMessage Текст ошибки
     *
     * @return void
     */
    protected function sendErrorNotification(?News $news, string $errorMessage): void
    {
        $message = "<b>Новость ID:</b> {$this->news_id} \r\n";

        if ($news) {
            $message .= "<b>Заголовок:</b> {$news->title} \r\n";
        }

        $message .= "<b>Ошибка:</b> \r\n<code>" . htmlspecialchars($errorMessage) . "</code>  \r\n";
        $message .= "🔗 <a href='" . Url::to(['admin/news/update', 'id' => $this->news_id], true)
            . "'>Редактировать новость</a>";

        $this->sendTelegramNotification($message, true);
    }
}
