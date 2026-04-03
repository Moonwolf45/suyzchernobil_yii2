<?php

namespace app\jobs;

use app\models\News;
use Yii;
use yii\httpclient\Client;

/**
 * Джоб для публикации новости в Одноклассниках
 */
class OkPublishJob extends SocialPublishJob
{
    /**
     * @var string Кеш URL серверов загрузки
     */
    private static $uploadUrlCache = null;

    /**
     * @inheritdoc
     */
    protected function getPublishedAtField(): string
    {
        return 'published_at_ok';
    }

    /**
     * @inheritdoc
     */
    protected function getSocialNetworkName(): string
    {
        return 'Одноклассники';
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

        // Формируем attachment
        $attachment = [
            'media' => [
                [
                    'type' => 'photo',
                    'list' => $uploadedImages
                ],
                [
                    'type' => 'text',
                    'text' => $message
                ]
            ]
        ];

        $attachmentJson = json_encode($attachment);

        try {
            $response = $this->executeWithRetry(function () use ($client, $attachmentJson) {
                return $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('https://api.ok.ru/api/mediatopic.post')
                    ->setData([
                        'application_key' => Yii::$app->params['OkAppPublicKey'],
                        'attachment' => $attachmentJson,
                        'gid' => Yii::$app->params['OkGroupId'],
                        'type' => 'GROUP_THEME',
                        'sig' => $this->calculateSignature($attachmentJson),
                        'access_token' => Yii::$app->params['OkApiKey']
                    ])
                    ->send();
            }, 'mediatopic.post');

            if ($response && $response->isOk) {
                $hasError = array_key_exists('error_code', (array) $response->data);

                Yii::info('mediatopic.post: ' . ($hasError ? 'ERROR' : 'SUCCESS'), 'jobs-ok');
                Yii::info($response->data ?? 'No data', 'jobs-ok');

                return !$hasError;
            }

            Yii::error('mediatopic.post: response is not ok', 'jobs-ok');

            return false;

        } catch (\Exception $e) {
            Yii::error('mediatopic.post exception: ' . $e->getMessage(), 'jobs-ok');

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
            $uploadedImage = $this->uploadSingleImageOk($client, $imagePath, $index);

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
                Yii::warning("Файл изображения не найден: {$imagePath}", 'jobs-ok');
                $failedCount++;

                continue;
            }

            try {
                $uploadedImage = $this->uploadSingleImageOk($client, $imagePath, $index);

                if ($uploadedImage !== null) {
                    $uploadedImages[] = $uploadedImage;
                    $uploadedCount++;
                    Yii::info("Картинка {$index} загружена", 'jobs-ok');
                } else {
                    Yii::warning("Не удалось загрузить картинку {$index}: {$imagePath}", 'jobs-ok');
                    $failedCount++;
                }

            } catch (\Exception $e) {
                Yii::error("Ошибка при загрузке картинки {$index}: " . $e->getMessage(), 'jobs-ok');
                $failedCount++;
                // Продолжаем загрузку следующих картинок
            }
        }

        if ($failedCount > 0) {
            Yii::warning(
                "Загружено {$uploadedCount} из " . count($images) .
                " картинок (ошибок: {$failedCount})",
                'jobs-ok'
            );
        }

        return $uploadedImages;
    }

    /**
     * Загружает одно изображение в Одноклассники
     *
     * @param Client $client
     * @param string $imagePath
     * @param int $index
     * @return array|null Токен загруженного изображения или null
     */
    private function uploadSingleImageOk(Client $client, string $imagePath, int $index): ?array
    {
        // Получаем URL с кешированием
        $uploadUrl = $this->getOkUploadUrl($client);
        
        if (!$uploadUrl) {
            Yii::error("Не удалось получить URL загрузки (image {$index})", 'jobs-ok');
            
            return null;
        }

        // Шаг 2: Загружаем изображение
        $fullPath = Yii::getAlias('@app/web/' . $imagePath);
        $fileHandle = fopen($fullPath, 'r');

        if ($fileHandle === false) {
            Yii::error("Не удалось открыть файл для загрузки (image {$index}): {$fullPath}", 'jobs-ok');

            return null;
        }

        try {
            $uploadResponse = $this->executeWithRetry(function () use ($client, $uploadUrl, $fileHandle) {
                return $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl($uploadUrl)
                    ->addFile('pic1', $fileHandle)
                    ->send();
            }, "photo upload (image {$index})");
        } finally {
            fclose($fileHandle);
        }

        if (!$uploadResponse || !$uploadResponse->isOk) {
            Yii::error("Не удалось загрузить фото на сервер (image {$index})", 'jobs-ok');

            return null;
        }

        if (array_key_exists('error_code', $uploadResponse->data)) {
            Yii::error(
                "API error upload: " . json_encode($uploadResponse->data['error_code']),
                'jobs-ok'
            );

            return null;
        }

        $photos = $uploadResponse->data['photos'] ?? [];
        if (!empty($photos)) {
            // Возвращаем токен первой картинки
            return ['id' => $photos[0]['token']];
        }

        Yii::error("Пустой массив photos в ответе", 'jobs-ok');

        return null;
    }

    /**
     * Вычисляет подпись для запроса к API Одноклассников
     *
     * @param string $attachmentJson
     * @return string
     */
    private function calculateSignature(string $attachmentJson): string
    {
        return md5(
            'application_key=' . Yii::$app->params['OkAppPublicKey'] .
            'attachment=' . $attachmentJson .
            'format=json' .
            'gid=' . Yii::$app->params['OkGroupId'] .
            'method=mediatopic.post' .
            'type=GROUP_THEME' .
            Yii::$app->params['OkAppSecretKey']
        );
    }

    /**
     * Получает URL сервера загрузки Одноклассников с кешированием
     *
     * @param Client $client
     * @return string
     */
    private function getOkUploadUrl(Client $client): string
    {
        // Проверяем кеш
        if (self::$uploadUrlCache !== null) {
            return self::$uploadUrlCache;
        }

        $method = 'photosV2.getUploadUrl';
        $sig = md5(
            'application_key=' . Yii::$app->params['OkAppPublicKey'] .
            'format=json' .
            'gid=' . Yii::$app->params['OkGroupId'] .
            'method=' . $method .
            Yii::$app->params['OkAppSecretKey']
        );

        $response = $this->executeWithRetry(function () use ($client, $method, $sig) {
            return $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.ok.ru/api/' . $method)
                ->setData([
                    'count' => 1,
                    'application_key' => Yii::$app->params['OkAppPublicKey'],
                    'access_token' => Yii::$app->params['OkApiKey'],
                    'gid' => Yii::$app->params['OkGroupId'],
                    'method' => $method,
                    'sig' => $sig
                ])
                ->send();
        }, 'photosV2.getUploadUrl');

        if (!$response || !$response->isOk) {
            $errorData = $response ? json_encode($response->data) : 'no response';
            Yii::error("getUploadUrl: response is not ok. Data: {$errorData}", 'jobs-ok');

            return '';
        }

        if (array_key_exists('error_code', $response->data)) {
            Yii::error(
                "API error getUploadUrl: " . json_encode($response->data),
                'jobs-ok'
            );

            return '';
        }

        $uploadUrl = $response->data['upload_url'] ?? null;

        if ($uploadUrl) {
            self::$uploadUrlCache = $uploadUrl;
        } else {
            Yii::error("getUploadUrl: upload_url not found in response: " . json_encode($response->data), 'jobs-ok');
        }

        return $uploadUrl ?? '';
    }
}
