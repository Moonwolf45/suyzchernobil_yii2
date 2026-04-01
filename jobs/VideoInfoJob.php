<?php

namespace app\jobs;

use app\models\NewsVideo;
use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
use yii\queue\JobInterface;

/**
 * Базовый класс для джобов получения информации о видео
 */
abstract class VideoInfoJob extends BaseObject implements JobInterface
{
    public $video_id;

    /**
     * Максимальное количество попыток при ошибке API
     */
    protected const MAX_RETRIES = 3;

    /**
     * Задержка между попытками (мс)
     */
    protected const RETRY_DELAY = 1000;

    /**
     * Плейсхолдер для превью при ошибке
     */
    protected const DEFAULT_PREVIEW_IMAGE = '/images/placeHolder.png';

    /**
     * @return string Название источника видео (YouTube, VK и т.д.)
     */
    protected abstract function getSourceName(): string;

    /**
     * @param string $videoUrl URL видео
     * @return array|null Данные видео (preview_image, duration) или null если не распознан
     */
    protected abstract function parseVideoUrl(string $videoUrl): ?array;

    /**
     * @param Client $client
     * @param array $videoData Распознанные данные видео
     * @return array|null Информация о видео из API или null при ошибке
     */
    protected abstract function fetchVideoInfo(Client $client, array $videoData): ?array;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        try {
            $newsVideo = $this->getVideo();

            if (!$newsVideo) {
                Yii::warning("Видео с ID {$this->video_id} не найдено", 'jobs-video');
                return;
            }

            // Если уже есть данные, не обновляем
            if (!empty($newsVideo->preview_image) && $newsVideo->preview_image !== self::DEFAULT_PREVIEW_IMAGE) {
                Yii::info("Видео {$this->video_id} уже имеет превью, пропускаем", 'jobs-video');
                return;
            }

            $videoData = $this->parseVideoUrl($newsVideo->video);

            if (!$videoData) {
                Yii::warning("Не удалось распознать URL видео: {$newsVideo->video}", 'jobs-video');
                $newsVideo->preview_image = self::DEFAULT_PREVIEW_IMAGE;
                $newsVideo->save(false);
                return;
            }

            $client = new Client();
            $info = $this->fetchVideoInfo($client, $videoData);

            if ($info) {
                $newsVideo->preview_image = $info['preview_image'] ?? self::DEFAULT_PREVIEW_IMAGE;
                $newsVideo->duration = $info['duration'] ?? null;
                Yii::info("Видео {$this->video_id} успешно обновлено", 'jobs-video');
            } else {
                Yii::warning("Не удалось получить информацию о видео {$this->video_id}", 'jobs-video');
                $newsVideo->preview_image = self::DEFAULT_PREVIEW_IMAGE;
            }

            $newsVideo->save(false);

        } catch (\Throwable $e) {
            Yii::error("Ошибка при обработке видео {$this->video_id}: " . $e->getMessage(), 'jobs-video');
            Yii::error($e->getTraceAsString(), 'jobs-video');

            // Сохраняем плейсхолдер при ошибке
            $this->savePlaceholder();
        }
    }

    /**
     * Получает видео из БД
     *
     * @return NewsVideo|null
     */
    protected function getVideo(): ?NewsVideo
    {
        return NewsVideo::find()->where(['id' => $this->video_id])->one();
    }

    /**
     * Выполняет запрос с повторными попытками при ошибке
     *
     * @param callable $requestCallback Функция, выполняющая запрос
     * @param string $operationName Название операции для логирования
     * @return mixed|null Результат запроса или null при неудаче
     */
    protected function executeWithRetry(callable $requestCallback, string $operationName)
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
                    'jobs-video'
                );
            }

            if ($attempt < self::MAX_RETRIES) {
                usleep(self::RETRY_DELAY * 1000);
            }
        }

        if ($lastException) {
            Yii::error(
                "Все попытки исчерпаны ({$operationName}): " . $lastException->getMessage(),
                'jobs-video'
            );
        }

        return null;
    }

    /**
     * Сохраняет плейсхолдер при ошибке
     */
    protected function savePlaceholder(): void
    {
        $newsVideo = $this->getVideo();
        if ($newsVideo) {
            $newsVideo->preview_image = self::DEFAULT_PREVIEW_IMAGE;
            $newsVideo->save(false);
        }
    }

    /**
     * Форматирует длительность из секунд в ISO 8601 формат (PT1H30M15S)
     *
     * @param int $seconds
     * @return string
     */
    protected function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $result = 'PT';
        if ($hours > 0) {
            $result .= $hours . 'H';
        }
        if ($minutes > 0) {
            $result .= $minutes . 'M';
        }
        $result .= $secs . 'S';

        return $result;
    }

    /**
     * Парсит длительность из ISO 8601 формата в секунды
     *
     * @param string $duration Строка длительности (например PT1H30M15S)
     * @return int Длительность в секундах
     */
    protected function parseDuration(string $duration): int
    {
        $interval = new \DateInterval($duration);

        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }
}
