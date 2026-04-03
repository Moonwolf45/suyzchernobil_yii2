<?php

namespace app\jobs;


use app\models\News;
use CURLFile;
use Yii;
use yii\httpclient\Client;

/**
 * Джоб для публикации новости ВКонтакте
 */
class VkPublishJob extends SocialPublishJob
{
    /**
     * @var string Версия API ВКонтакте
     */
    private const VK_API_VERSION = '5.131';

    /**
     * @var ?string Кеш URL серверов загрузки
     */
    private static ?string $uploadUrlCache = null;

    /**
     * @inheritdoc
     */
    protected function getPublishedAtField(): string
    {
        return 'published_at_vk';
    }

    /**
     * @inheritdoc
     */
    protected function getSocialNetworkName(): string
    {
        return 'ВКонтакте';
    }

    /**
     * @inheritdoc
     */
    protected function publish(Client $client, News $news, int &$uploadedImagesCount = 0, int &$failedImagesCount = 0): bool
    {
        $message = $this->prepareMessage($news);
        $images = $this->getImages($news);

        // Загружаем картинки с отказоустойчивостью
        $uploadedImages = $this->uploadImagesWithFallback($client, $images, $uploadedImagesCount, $failedImagesCount);

        // Если картинки не загрузились — публикуем только текст
        $attachments = !empty($uploadedImages) ? implode(',', $uploadedImages) : '';

        try {
            $response = $this->executeWithRetry(function () use ($client, $message, $attachments) {
                $request = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('https://api.vk.com/method/wall.post')
                    ->setData([
                        'owner_id' => '-' . Yii::$app->params['VkGroupId'],
                        'friends_only' => 0,
                        'from_group' => 1,
                        'message' => $message,
                        'attachments' => $attachments,
                        'access_token' => Yii::$app->params['VkApiKey'],
                        'v' => self::VK_API_VERSION
                    ]);

                Yii::info("REQUEST [wall.post]: POST https://api.vk.com/method/wall.post", 'jobs-vk');

                $resp = $request->send();

                Yii::info("RESPONSE [wall.post]: " . json_encode($resp->data), 'jobs-vk');

                return $resp;
            }, 'wall.post');

            if ($response && $response->isOk) {
                $hasError = array_key_exists('error', $response->data);

                Yii::info('wall.post: ' . ($hasError ? 'ERROR' : 'SUCCESS'), 'jobs-vk');
                Yii::info($response->data ?? 'No data', 'jobs-vk');

                return !$hasError;
            }

            Yii::error('wall.post: response is not ok', 'jobs-vk');

            return false;

        } catch (\Exception $e) {
            Yii::error('wall.post exception: ' . $e->getMessage(), 'jobs-vk');

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    protected function uploadImages(Client $client, array $images): array
    {
        $result = [];

        foreach ($images as $index => $imagePath) {
            $uploadedImage = $this->uploadSingleImageVk($client, $imagePath, $index);

            if ($uploadedImage !== null) {
                $result[] = $uploadedImage;
            }
        }

        return $result;
    }

    /**
     * Загружает картинки с продолжением работы при ошибках
     *
     * @param Client $client
     * @param array $images
     * @param int &$uploadedCount Счётчик загруженных изображений (выходной параметр)
     * @param int &$failedCount Счётчик неудачных загрузок (выходной параметр)
     *
     * @return array
     */
    private function uploadImagesWithFallback(Client $client, array $images, int &$uploadedCount = 0, int &$failedCount = 0): array
    {
        $uploadedImages = [];

        foreach ($images as $index => $imagePath) {
            // Проверяем существование файла
            if (!$this->imageExists($imagePath)) {
                Yii::warning("Файл изображения не найден: {$imagePath}", 'jobs-vk');
                $failedCount++;

                continue;
            }

            try {
                $uploadedImage = $this->uploadSingleImageVk($client, $imagePath, $index);

                if ($uploadedImage !== null) {
                    $uploadedImages[] = $uploadedImage;
                    $uploadedCount++;
                    Yii::info("Картинка {$index} загружена: {$uploadedImage}", 'jobs-vk');
                } else {
                    Yii::warning("Не удалось загрузить картинку {$index}: {$imagePath}", 'jobs-vk');
                    $failedCount++;
                }

            } catch (\Exception $e) {
                Yii::error("Ошибка при загрузке картинки {$index}: " . $e->getMessage(), 'jobs-vk');
                $failedCount++;
                // Продолжаем загрузку следующих картинок
            }
        }

        if ($failedCount > 0) {
            Yii::warning(
                "Загружено {$uploadedCount} из " . count($images) .
                " картинок (ошибок: {$failedCount})",
                'jobs-vk'
            );
        }

        return $uploadedImages;
    }

    /**
     * Загружает одно изображение ВКонтакте
     *
     * @param Client $client
     * @param string $imagePath
     * @param int $index
     * @return string|null Идентификатор загруженного изображения или null
     */
    private function uploadSingleImageVk(Client $client, string $imagePath, int $index): ?string
    {
        // Получаем URL с кешированием
        $uploadUrl = $this->getVkUploadUrl($client);
        if (!$uploadUrl) {
            Yii::error("Не удалось получить сервер загрузки (image {$index})", 'jobs-vk');

            return null;
        }

        $fullPath = Yii::getAlias('@app/web/' . $imagePath);
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Yii::error("Файл не существует или не читаем (image {$index}): {$fullPath}", 'jobs-vk');

            return null;
        }

        $uploadResponse = $this->executeWithRetry(function () use ($client, $uploadUrl, $fullPath, $index) {
            $request = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($uploadUrl)
                ->addFile('photo', new \CurlFile($fullPath))
                ->addHeaders([
                    'Content-Type' => 'multipart/form-data; charset=UTF-8',
                ]);

            Yii::info("REQUEST [photo upload]: POST {$uploadUrl}", 'jobs-vk');
            Yii::info("REQUEST [photo upload]: POST photo: {$fullPath}", 'jobs-vk');

            $response = $request->send();

            Yii::info("RESPONSE [photo upload]: " . json_encode($response->data), 'jobs-vk');

            return $response;
        }, "photo upload (image {$index})");

        if (!$uploadResponse || !$uploadResponse->isOk) {
            Yii::error("Не удалось загрузить фото на сервер (image {$index})", 'jobs-vk');

            return null;
        }

        if (array_key_exists('error', $uploadResponse->data)) {
            Yii::error("API error upload: " . json_encode($uploadResponse->data['error']), 'jobs-vk');

            return null;
        }

        $photo = $uploadResponse->data['photo'] ?? null;
        $server = $uploadResponse->data['server'] ?? null;
        $hash = $uploadResponse->data['hash'] ?? null;

        if (empty($photo) && mb_strlen($photo) <= 2) {
            Yii::error("Некорректный photo параметр", 'jobs-vk');

            return null;
        }

        $saveResponse = $this->executeWithRetry(function () use ($client, $photo, $server, $hash, $index) {
            $request = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api.vk.com/method/photos.saveWallPhoto')
                ->setData([
                    'photo' => $photo,
                    'server' => $server,
                    'hash' => $hash,
                    'group_id' => Yii::$app->params['VkGroupId'],
                    'access_token' => Yii::$app->params['VkApiKey'],
                    'v' => self::VK_API_VERSION
                ]);

            Yii::info("REQUEST [photos.saveWallPhoto] (image {$index}): GET https://api.vk.com/method/photos.saveWallPhoto", 'jobs-vk');

            $resp = $request->send();

            Yii::info("REQUEST [photos.saveWallPhoto] (image {$index}): GET {$request->getFullUrl()}", 'jobs-vk');
            Yii::info("RESPONSE [photos.saveWallPhoto] (image {$index}): " . json_encode($resp->data), 'jobs-vk');

            return $resp;
        }, "photos.saveWallPhoto (image {$index})");

        if (!$saveResponse || !$saveResponse->isOk) {
            Yii::error("Не удалось сохранить фото (image {$index})", 'jobs-vk');

            return null;
        }

        if (array_key_exists('error', $saveResponse->data)) {
            Yii::error("API error saveWallPhoto: " . json_encode($saveResponse->data['error']), 'jobs-vk');

            return null;
        }

        $savedImages = $saveResponse->data['response'] ?? [];
        if (!empty($savedImages)) {
            $savedImage = $savedImages[0];

            return 'photo' . $savedImage['owner_id'] . '_' . $savedImage['id'];
        }

        Yii::error("Пустой ответ от saveWallPhoto", 'jobs-vk');

        return null;
    }

    /**
     * Получает URL сервера загрузки ВКонтакте с кешированием
     *
     * @param Client $client
     * @return string|null
     */
    private function getVkUploadUrl(Client $client): ?string
    {
        if (self::$uploadUrlCache !== null) {
            return self::$uploadUrlCache;
        }

        $response = $this->executeWithRetry(function () use ($client) {
            return $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api.vk.com/method/photos.getWallUploadServer')
                ->setData([
                    'group_id' => Yii::$app->params['VkGroupId'],
                    'access_token' => Yii::$app->params['VkApiKey'],
                    'v' => self::VK_API_VERSION
                ])
                ->send();
        }, 'photos.getWallUploadServer');

        if (!$response || !$response->isOk) {
            return null;
        }

        if (array_key_exists('error', $response->data)) {
            Yii::error("API error getWallUploadServer: " . $response->data['error'], 'jobs-vk');

            return null;
        }

        $uploadUrl = $response->data['response']['upload_url'] ?? null;
        
        if ($uploadUrl) {
            self::$uploadUrlCache = $uploadUrl;
        }

        return $uploadUrl;
    }
}
