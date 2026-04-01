<?php

namespace app\jobs;

use Yii;
use yii\httpclient\Client;

/**
 * Джоб для получения информации о видео из YouTube
 */
class YoutubeVideoJob extends VideoInfoJob
{
    /**
     * @var string Версия API YouTube
     */
    private const YOUTUBE_API_VERSION = 'v3';

    /**
     * Регулярные выражения для парсинга URL YouTube
     */
    private const URL_PATTERNS = [
        // youtube.com/embed/VIDEO_ID
        '#(?:www\.)?youtube\.com/embed/([a-zA-Z0-9_\-]+)#i',
        // youtube.com/watch?v=VIDEO_ID
        '#(?:www\.)?youtube\.com/watch\?v=([a-zA-Z0-9_\-]+)#i',
        // youtu.be/VIDEO_ID
        '#(?:www\.)?youtu\.be/([a-zA-Z0-9_\-]+)#i',
        // youtube.com/v/VIDEO_ID
        '#(?:www\.)?youtube\.com/v/([a-zA-Z0-9_\-]+)#i',
    ];

    /**
     * @inheritdoc
     */
    protected function getSourceName(): string
    {
        return 'YouTube';
    }

    /**
     * @inheritdoc
     */
    protected function parseVideoUrl(string $videoUrl): ?array
    {
        foreach (self::URL_PATTERNS as $pattern) {
            if (preg_match($pattern, $videoUrl, $matches)) {
                return ['video_id' => $matches[1]];
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function fetchVideoInfo(Client $client, array $videoData): ?array
    {
        $videoId = $videoData['video_id'] ?? null;

        if (!$videoId) {
            Yii::error('Не указан video_id для YouTube', 'jobs-video');

            return null;
        }

        $response = $this->executeWithRetry(function () use ($client, $videoId) {
            return $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://www.googleapis.com/youtube/v3/videos')
                ->setData([
                    'id' => $videoId,
                    'key' => Yii::$app->params['YoutubeApiKey'],
                    'part' => 'contentDetails,snippet'
                ])
                ->send();
        }, 'YouTube API');

        if (!$response || !$response->isOk) {
            Yii::error('YouTube API: ответ не получен или ошибка HTTP', 'jobs-video');

            return null;
        }

        // Проверяем наличие ошибок в ответе API
        if (isset($response->data['error'])) {
            Yii::error('YouTube API error: ' . json_encode($response->data['error']), 'jobs-video');

            return null;
        }

        // Проверяем наличие элементов в ответе
        if (empty($response->data['items']) || !is_array($response->data['items'])) {
            Yii::warning('YouTube API: видео не найдено или недоступно', 'jobs-video');

            return null;
        }

        $item = $response->data['items'][0];

        // Формируем результат
        $result = [
            // Превью высокого качества
            'preview_image' => 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg',
        ];

        // Получаем длительность
        if (isset($item['contentDetails']['duration'])) {
            $result['duration'] = $item['contentDetails']['duration'];
        }

        // Проверяем доступность превью
        if (!$this->checkImageUrl($result['preview_image'])) {
            // Если maxresdefault недоступно, используем среднее качество
            $result['preview_image'] = 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        return $result;
    }

    /**
     * Проверяет доступность URL изображения
     *
     * @param string $url
     * @return bool
     */
    private function checkImageUrl(string $url): bool
    {
        try {
            $headers = @get_headers($url);
            return $headers && strpos($headers[0], '200') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
