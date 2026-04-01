<?php

namespace app\jobs;

use Yii;
use yii\httpclient\Client;

/**
 * Джоб для получения информации о видео из ВКонтакте
 */
class VkVideoJob extends VideoInfoJob
{
    /**
     * @var string Версия API ВКонтакте
     */
    private const VK_API_VERSION = '5.131';

    /**
     * Регулярные выражения для парсинга URL ВКонтакте
     */
    private const URL_PATTERNS = [
        // vk.com/video_ext.php?oid=...&id=...
        '#(?:www\.)?vk\.com/video_ext\.php\?([^#]+)#i',
        // vk.com/video-123456_789012345
        '#(?:www\.)?vk\.com/video(-?\d+)_(\d+)#i',
        // vk.com/clip-123456_789012345
        '#(?:www\.)?vk\.com/clip(-?\d+)_(\d+)#i',
    ];

    /**
     * @inheritdoc
     */
    protected function getSourceName(): string
    {
        return 'ВКонтакте';
    }

    /**
     * @inheritdoc
     */
    protected function parseVideoUrl(string $videoUrl): ?array
    {
        // Паттерн для video_ext.php
        if (preg_match(self::URL_PATTERNS[0], $videoUrl, $matches)) {
            parse_str($matches[1], $params);

            if (isset($params['oid']) && isset($params['id'])) {
                return [
                    'owner_id' => $params['oid'],
                    'video_id' => $params['id'],
                ];
            }
        }

        // Паттерн для vk.com/video-123456_789012345
        if (preg_match(self::URL_PATTERNS[1], $videoUrl, $matches)) {
            return [
                'owner_id' => $matches[1],
                'video_id' => $matches[2],
            ];
        }

        // Паттерн для vk.com/clip-123456_789012345
        if (preg_match(self::URL_PATTERNS[2], $videoUrl, $matches)) {
            return [
                'owner_id' => $matches[1],
                'video_id' => $matches[2],
            ];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function fetchVideoInfo(Client $client, array $videoData): ?array
    {
        $ownerId = $videoData['owner_id'] ?? null;
        $videoId = $videoData['video_id'] ?? null;

        if (!$ownerId || !$videoId) {
            Yii::error('Не указаны owner_id или video_id для VK', 'jobs-video');

            return null;
        }

        $videoParam = $ownerId . '_' . $videoId;

        $response = $this->executeWithRetry(function () use ($client, $videoParam) {
            return $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api.vk.com/method/video.get')
                ->setData([
                    'videos' => $videoParam,
                    'access_token' => Yii::$app->params['VkApiKey'],
                    'v' => self::VK_API_VERSION
                ])
                ->send();
        }, 'VK Video API');

        if (!$response || !$response->isOk) {
            Yii::error('VK Video API: ответ не получен или ошибка HTTP', 'jobs-video');

            return null;
        }

        // Проверяем наличие ошибок в ответе API
        if (isset($response->data['error'])) {
            Yii::error('VK Video API error: ' . json_encode($response->data['error']), 'jobs-video');

            return null;
        }

        // Проверяем наличие элементов в ответе
        if (empty($response->data['response']['items']) || !is_array($response->data['response']['items'])) {
            Yii::warning('VK Video API: видео не найдено или недоступно', 'jobs-video');
            return null;
        }

        $item = $response->data['response']['items'][0];

        // Получаем превью (берём изображение с наибольшим разрешением)
        $previewImage = $this->getBestImage($item);

        // Получаем длительность и конвертируем в ISO 8601 формат
        $duration = null;
        if (isset($item['duration']) && is_numeric($item['duration'])) {
            $duration = $this->formatDuration((int) $item['duration']);
        }

        return [
            'preview_image' => $previewImage,
            'duration' => $duration,
        ];
    }

    /**
     * Получает изображение наилучшего качества из доступных
     *
     * @param array $item Данные видео
     * @return string URL изображения
     */
    private function getBestImage(array $item): string
    {
        // Новые API возвращают image в виде массива с разными размерами
        if (!empty($item['image']) && is_array($item['image'])) {
            // Сортируем по размеру (предполагаем, что последний — самый большой)
            $images = $item['image'];
            usort($images, function ($a, $b) {
                $widthA = $a['width'] ?? 0;
                $widthB = $b['width'] ?? 0;

                return $widthB - $widthA;
            });

            if (!empty($images[0]['url'])) {
                return $images[0]['url'];
            }
        }

        // Старый формат: photo_130, photo_640, photo_800, photo_1280
        $imageKeys = ['photo_1280', 'photo_800', 'photo_640', 'photo_320', 'photo_130'];

        foreach ($imageKeys as $key) {
            if (!empty($item[$key])) {
                return $item[$key];
            }
        }

        // Если ничего не найдено, возвращаем плейсхолдер
        return self::DEFAULT_PREVIEW_IMAGE;
    }
}
